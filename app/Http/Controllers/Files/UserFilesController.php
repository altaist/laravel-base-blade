<?php

namespace App\Http\Controllers\Files;

use App\Models\File;
use App\Services\Files\FileService;
use App\Http\Controllers\Files\FileOperationsTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;

class UserFilesController extends Controller
{
    use FileOperationsTrait;

    public function __construct(
        FileService $fileService
    ) {
        $this->fileService = $fileService;
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $files = $this->fileService->getUserFiles($user->id, 20);

        return view('pages.user.files.index', compact('files'));
    }

    public function upload(Request $request)
    {
        $result = $this->uploadMultipleFiles($request, true);
        
        if ($request->expectsJson()) {
            return $result;
        }

        // Для веб-интерфейса обрабатываем JSON ответ
        $data = $result->getData(true);
        
        if ($data['success']) {
            return redirect()->route('user.files.index')
                ->with('success', "Загружено файлов: {$data['success_count']}")
                ->with('errors', $data['errors'] ?? []);
        } else {
            return redirect()->route('user.files.index')
                ->with('error', $data['message']);
        }
    }

    public function download(File $file)
    {
        $result = $this->downloadFile($file, true);
        
        // Если это JSON ответ (ошибка), перенаправляем с сообщением
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            $data = $result->getData(true);
            return redirect()->route('user.files.index')
                ->with('error', $data['message']);
        }
        
        return $result;
    }

    public function delete(File $file)
    {
        $result = $this->deleteFile($file, true);
        
        if (request()->expectsJson()) {
            return $result;
        }

        // Для веб-интерфейса обрабатываем JSON ответ
        $data = $result->getData(true);
        
        if ($data['success']) {
            return redirect()->route('user.files.index')
                ->with('success', 'Файл удален');
        } else {
            return redirect()->route('user.files.index')
                ->with('error', $data['message']);
        }
    }

    public function togglePublic(File $file)
    {
        $result = $this->toggleFilePublic($file, true);
        
        if (request()->expectsJson()) {
            return $result;
        }

        // Для веб-интерфейса обрабатываем JSON ответ
        $data = $result->getData(true);
        
        if ($data['success']) {
            return redirect()->route('user.files.index')
                ->with('success', $data['message']);
        } else {
            return redirect()->route('user.files.index')
                ->with('error', $data['message']);
        }
    }

    public function downloadMultiple(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array|min:1|max:50',
            'file_ids.*' => 'integer|exists:files,id'
        ]);

        $user = Auth::user();
        $fileIds = $request->input('file_ids');
        
        // Получаем файлы пользователя
        $files = $this->fileService->getUserFilesByIds($user->id, $fileIds);

        if ($files->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Файлы не найдены'
            ], 404);
        }

        try {
            // Создаем временную директорию для файлов
            $tempDir = storage_path('app/temp/' . uniqid('files_', true));
            $zipPath = $tempDir . '.zip';
            
            // Создаем директорию temp если её нет
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Создаем временную директорию для файлов
            mkdir($tempDir, 0755, true);

            $addedFiles = 0;
            foreach ($files as $file) {
                if (Storage::disk($file->disk)->exists($file->path)) {
                    $sourcePath = Storage::disk($file->disk)->path($file->path);
                    $destPath = $tempDir . '/' . $file->original_name;
                    
                    // Копируем файл в временную директорию
                    if (copy($sourcePath, $destPath)) {
                        $addedFiles++;
                    }
                }
            }

            if ($addedFiles === 0) {
                $this->cleanupTempDir($tempDir);
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось добавить файлы в архив'
                ], 400);
            }

            // Пытаемся создать ZIP-архив с помощью системной команды
            $result = Process::run("cd " . dirname($tempDir) . " && zip -r " . basename($zipPath) . " " . basename($tempDir));
            
            if (!$result->successful()) {
                // Если zip недоступен, возвращаем первый файл
                $this->cleanupTempDir($tempDir);
                if (file_exists($zipPath)) {
                    unlink($zipPath);
                }
                
                // Fallback: возвращаем первый файл
                $firstFile = $files->first();
                $filePath = Storage::disk($firstFile->disk)->path($firstFile->path);
                return response()->download($filePath, $firstFile->original_name);
            }

            // Удаляем временную директорию
            $this->cleanupTempDir($tempDir);

            if (!file_exists($zipPath)) {
                throw new \Exception('ZIP-файл не был создан');
            }

            // Отправляем ZIP-файл
            return response()->download($zipPath, 'files.zip')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Очищаем временные файлы в случае ошибки
            if (isset($tempDir)) {
                $this->cleanupTempDir($tempDir);
            }
            if (isset($zipPath) && file_exists($zipPath)) {
                unlink($zipPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании архива: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Рекурсивно удаляет временную директорию
     */
    private function cleanupTempDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->cleanupTempDir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * Получить изображения пользователя
     */
    public function getImages(Request $request)
    {
        $user = Auth::user();
        $images = $this->fileService->getUserImages($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'images' => $images->map(fn($file) => $this->formatFileResponse($file))
            ]);
        }

        return view('user.files.images', compact('images'));
    }

    /**
     * Получить документы пользователя
     */
    public function getDocuments(Request $request)
    {
        $user = Auth::user();
        $documents = $this->fileService->getUserDocuments($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'documents' => $documents->map(fn($file) => $this->formatFileResponse($file))
            ]);
        }

        return view('user.files.documents', compact('documents'));
    }

    /**
     * Получить статистику файлов пользователя
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $stats = $this->fileService->getUserFileStats($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        }

        return view('user.files.stats', compact('stats'));
    }
}
