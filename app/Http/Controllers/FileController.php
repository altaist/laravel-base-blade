<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\Files\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function __construct(
        private FileService $fileService
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        try {
            $file = $this->fileService->upload(
                $request->file('file'),
                auth()->id(),
                $request->boolean('is_public', false)
            );

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $file->id,
                    'original_name' => $file->original_name,
                    'size' => $file->size,
                    'mime_type' => $file->mime_type,
                    'public_url' => $file->public_url,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function download(File $file): BinaryFileResponse|JsonResponse
    {
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

    public function createPublicUrl(File $file): JsonResponse
    {
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

    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240', // 10MB per file
        ]);

        try {
            $result = $this->fileService->uploadMultiple(
                $request->file('files'),
                auth()->id(),
                $request->boolean('is_public', false)
            );

            return response()->json([
                'success' => true,
                'uploaded_files' => $result['files']->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'original_name' => $file->original_name,
                        'size' => $file->size,
                        'mime_type' => $file->mime_type,
                        'public_url' => $file->public_url,
                    ];
                }),
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

    public function showImage(File $file): BinaryFileResponse|JsonResponse
    {
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
