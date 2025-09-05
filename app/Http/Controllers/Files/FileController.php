<?php

namespace App\Http\Controllers\Files;

use App\Models\File;
use App\Services\Files\FileService;
use App\Helpers\FileHelper;
use App\Http\Controllers\Files\FileOperationsTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    use FileOperationsTrait;

    public function __construct(
        FileService $fileService
    ) {
        $this->fileService = $fileService;
    }

    public function upload(Request $request): JsonResponse
    {
        return $this->uploadSingleFile($request, true);
    }

    public function download(File $file): BinaryFileResponse|JsonResponse
    {
        return $this->downloadFile($file, false);
    }

    public function publicDownload(string $key): BinaryFileResponse|JsonResponse
    {
        $file = File::where('key', $key)->where('is_public', true)->first();
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден'
            ], 404);
        }

        return $this->download($file);
    }

    public function delete(File $file): JsonResponse
    {
        return $this->deleteFile($file, false);
    }

    public function createPublicUrl(File $file): JsonResponse
    {
        return $this->createPublicUrl($file, false);
    }

    public function uploadMultiple(Request $request): JsonResponse
    {
        return $this->uploadMultipleFiles($request, true);
    }

    public function showImage(File $file): BinaryFileResponse|JsonResponse
    {
        // Проверяем, что пользователь имеет доступ к файлу
        if (!Auth::check() || (!$this->fileService->isFileOwnedByUser($file, Auth::id()) && !$file->is_public)) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        if (!FileHelper::isImage($file->mime_type)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не является изображением'
            ], 400);
        }

        try {
            $filePath = $this->fileService->download($file);
            
            return response()->file($filePath, [
                'Content-Type' => $file->mime_type,
                'Cache-Control' => 'public, max-age=3600',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function showPublicImage(string $key): BinaryFileResponse|JsonResponse
    {
        $file = File::where('key', $key)
            ->where('is_public', true)
            ->first();
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Изображение не найдено'
            ], 404);
        }

        return $this->showImage($file);
    }
}
