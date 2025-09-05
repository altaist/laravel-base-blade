<?php

namespace App\Services\Files;

use App\Models\File;
use App\Helpers\FileHelper;
use App\Helpers\ImgHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FileService
{
    public function upload(UploadedFile $file, ?int $userId = null, bool $isPublic = false): File
    {
        $errors = FileHelper::validateFile($file);
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        return DB::transaction(function () use ($file, $userId, $isPublic) {
            // Создаем запись в БД
            $fileRecord = File::create([
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => FileHelper::getFileExtension($file->getClientOriginalName()),
                'disk' => 'files',
                'path' => $file->getClientOriginalName(),
                'key' => $isPublic ? FileHelper::generateKey() : null,
                'is_public' => $isPublic,
                'user_id' => $userId,
            ]);

            // Сохраняем файл с именем = ID
            $filename = $fileRecord->id . '.' . $fileRecord->extension;
            $path = $filename;
            
            Storage::disk($fileRecord->disk)->put($path, file_get_contents($file));
            
            // Обновляем путь
            $fileRecord->update(['path' => $path]);

            // Обрабатываем изображения
            if (FileHelper::isImage($fileRecord->mime_type)) {
                $fullPath = Storage::disk($fileRecord->disk)->path($path);
                ImgHelper::optimize($fullPath);
            }

            return $fileRecord;
        });
    }

    public function download(File $file): string
    {
        if (!Storage::disk($file->disk)->exists($file->path)) {
            throw new \Exception('Файл не найден');
        }

        return Storage::disk($file->disk)->path($file->path);
    }

    public function delete(File $file): bool
    {
        return DB::transaction(function () use ($file) {
            // Удаляем физический файл
            if (Storage::disk($file->disk)->exists($file->path)) {
                Storage::disk($file->disk)->delete($file->path);
            }
            
            // Удаляем запись из БД
            return $file->delete();
        });
    }

    /**
     * Безопасное удаление файла с проверкой attachments
     */
    public function safeDelete(File $file): array
    {
        return DB::transaction(function () use ($file) {
            // Проверяем, используется ли файл в attachments
            $attachmentsCount = \App\Models\Attachment::where('file_id', $file->id)->count();
            
            if ($attachmentsCount > 0) {
                return [
                    'success' => false,
                    'message' => "Файл используется в {$attachmentsCount} attachment(s). Сначала удалите attachments.",
                    'attachments_count' => $attachmentsCount
                ];
            }
            
            // Если файл не используется, удаляем его
            $deleted = $this->delete($file);
            
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Файл успешно удален' : 'Ошибка при удалении файла'
            ];
        });
    }

    public function createPublicUrl(File $file): string
    {
        if ($file->is_public && $file->key) {
            return $file->public_url;
        }

        $file->update([
            'is_public' => true,
            'key' => FileHelper::generateKey(),
        ]);

        return $file->public_url;
    }

    public function uploadMultiple(array $files, ?int $userId = null, bool $isPublic = false): array
    {
        $uploadedFiles = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                $uploadedFiles[] = $this->upload($file, $userId, $isPublic);
            } catch (\Exception $e) {
                $errors[] = "Файл #{$index}: " . $e->getMessage();
            }
        }

        return [
            'files' => collect($uploadedFiles),
            'errors' => $errors,
            'success_count' => count($uploadedFiles),
            'error_count' => count($errors),
        ];
    }

    /**
     * Получить файлы пользователя
     */
    public function getUserFiles(int $userId, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return File::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Получить файлы пользователя по ID
     */
    public function getUserFilesByIds(int $userId, array $fileIds): \Illuminate\Database\Eloquent\Collection
    {
        return File::where('user_id', $userId)
            ->whereIn('id', $fileIds)
            ->get();
    }

    /**
     * Получить изображения пользователя
     */
    public function getUserImages(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return File::where('user_id', $userId)
            ->where('mime_type', 'like', 'image/%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Получить документы пользователя
     */
    public function getUserDocuments(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return File::where('user_id', $userId)
            ->where('mime_type', 'not like', 'image/%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Проверить, принадлежит ли файл пользователю
     */
    public function isFileOwnedByUser(File $file, int $userId): bool
    {
        return $file->user_id === $userId;
    }

    /**
     * Получить статистику файлов пользователя
     */
    public function getUserFileStats(int $userId): array
    {
        $files = File::where('user_id', $userId)->get();
        
        return [
            'total_files' => $files->count(),
            'total_size' => $files->sum('size'),
            'images_count' => $files->where('mime_type', 'like', 'image/%')->count(),
            'documents_count' => $files->where('mime_type', 'not like', 'image/%')->count(),
            'public_files' => $files->where('is_public', true)->count(),
            'private_files' => $files->where('is_public', false)->count(),
        ];
    }
}
