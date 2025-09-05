<?php

namespace App\Http\Controllers\Files;

use App\Models\File;
use App\Services\Files\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait FileOperationsTrait
{
    protected FileService $fileService;

    /**
     * Загрузка одного файла
     */
    protected function uploadSingleFile(Request $request, bool $requireAuth = true): JsonResponse
    {
        if ($requireAuth && !Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация'
            ], 401);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        try {
            $file = $this->fileService->upload(
                $request->file('file'),
                Auth::id(),
                $request->boolean('is_public', false)
            );

            return response()->json([
                'success' => true,
                'file' => $this->formatFileResponse($file)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Загрузка множественных файлов
     */
    protected function uploadMultipleFiles(Request $request, bool $requireAuth = true): JsonResponse
    {
        if ($requireAuth && !Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Требуется авторизация'
            ], 401);
        }

        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240', // 10MB per file
        ]);

        try {
            $result = $this->fileService->uploadMultiple(
                $request->file('files'),
                Auth::id(),
                $request->boolean('is_public', false)
            );

            return response()->json([
                'success' => true,
                'uploaded_files' => $result['files']->map(fn($file) => $this->formatFileResponse($file)),
                'errors' => $result['errors'],
                'success_count' => $result['success_count'],
                'error_count' => $result['error_count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Скачивание файла
     */
    protected function downloadFile(File $file, bool $checkOwnership = false): BinaryFileResponse|JsonResponse
    {
        if ($checkOwnership && !$this->fileService->isFileOwnedByUser($file, Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        try {
            $filePath = $this->fileService->download($file);
            return response()->download($filePath, $file->original_name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Удаление файла
     */
    protected function deleteFile(File $file, bool $checkOwnership = false): JsonResponse
    {
        if ($checkOwnership && !$this->fileService->isFileOwnedByUser($file, Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        try {
            $this->fileService->delete($file);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Создание публичной ссылки
     */
    protected function createPublicUrl(File $file, bool $checkOwnership = false): JsonResponse
    {
        if ($checkOwnership && !$this->fileService->isFileOwnedByUser($file, Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        try {
            $url = $this->fileService->createPublicUrl($file);
            return response()->json([
                'success' => true,
                'public_url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Переключение публичности файла
     */
    protected function toggleFilePublic(File $file, bool $checkOwnership = false): JsonResponse
    {
        if ($checkOwnership && !$this->fileService->isFileOwnedByUser($file, Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403);
        }

        try {
            if ($file->is_public) {
                $file->update(['is_public' => false, 'key' => null]);
                $message = 'Файл сделан приватным';
            } else {
                $this->fileService->createPublicUrl($file);
                $message = 'Файл сделан публичным';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_public' => $file->fresh()->is_public,
                'public_url' => $file->fresh()->public_url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Форматирование ответа с данными файла
     */
    protected function formatFileResponse(File $file): array
    {
        return [
            'id' => $file->id,
            'original_name' => $file->original_name,
            'size' => $file->size,
            'mime_type' => $file->mime_type,
            'public_url' => $file->public_url,
            'created_at' => $file->created_at->format('d.m.Y H:i'),
        ];
    }
}
