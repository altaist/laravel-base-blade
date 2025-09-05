<?php

namespace App\Services\Files;

use App\Enums\AttachmentType;
use App\Models\Attachment;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttachmentService
{
    public function __construct(
        private FileService $fileService
    ) {}

    /**
     * Создать attachment с загруженным файлом
     */
    public function createAttachment(
        Model $relatedModel,
        UploadedFile $file,
        ?string $name = null,
        ?string $description = null,
        AttachmentType $type = null
    ): Attachment {
        return DB::transaction(function () use ($relatedModel, $file, $name, $description, $type) {
            // Загружаем файл
            $userId = Auth::check() ? Auth::id() : null;
            $fileRecord = $this->fileService->upload($file, $userId, false);
            
            // Определяем тип attachment
            $attachmentType = $type ?? $this->determineAttachmentType($fileRecord->mime_type);
            
            // Создаем attachment
            return Attachment::create([
                'related_type' => get_class($relatedModel),
                'related_id' => $relatedModel->id,
                'file_id' => $fileRecord->id,
                'name' => $name,
                'description' => $description,
                'type' => $attachmentType,
            ]);
        });
    }

    /**
     * Создать attachment с внешней ссылкой
     */
    public function createAttachmentFromUrl(
        Model $relatedModel,
        string $url,
        ?string $name = null,
        ?string $description = null,
        AttachmentType $type = AttachmentType::IMAGE
    ): Attachment {
        return Attachment::create([
            'related_type' => get_class($relatedModel),
            'related_id' => $relatedModel->id,
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'type' => $type,
        ]);
    }

    /**
     * Удалить attachment
     */
    public function deleteAttachment(Attachment $attachment, bool $deleteFile = false): bool
    {
        return DB::transaction(function () use ($attachment, $deleteFile) {
            // Удаляем файл, если нужно
            if ($deleteFile && $attachment->file) {
                $this->fileService->delete($attachment->file);
            }
            
            // Удаляем attachment
            return $attachment->delete();
        });
    }

    /**
     * Получить attachments для модели
     */
    public function getAttachmentsForModel(Model $model, ?AttachmentType $type = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Attachment::where('related_type', get_class($model))
            ->where('related_id', $model->id);
            
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->with('file')->get();
    }

    /**
     * Получить изображения для модели
     */
    public function getImagesForModel(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getAttachmentsForModel($model, AttachmentType::IMAGE);
    }

    /**
     * Получить документы для модели
     */
    public function getDocumentsForModel(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return $this->getAttachmentsForModel($model, AttachmentType::DOCUMENT);
    }

    /**
     * Определить тип attachment по MIME типу файла
     */
    private function determineAttachmentType(string $mimeType): AttachmentType
    {
        if (str_starts_with($mimeType, 'image/')) {
            return AttachmentType::IMAGE;
        }
        
        return AttachmentType::DOCUMENT;
    }

    /**
     * Проверить, используется ли файл в attachments
     */
    public function isFileUsedInAttachments(File $file): bool
    {
        return Attachment::where('file_id', $file->id)->exists();
    }

    /**
     * Получить количество attachments для файла
     */
    public function getAttachmentsCountForFile(File $file): int
    {
        return Attachment::where('file_id', $file->id)->count();
    }
}
