<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\Files\FileService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserFilesController extends Controller
{
    public function __construct(
        private FileService $fileService
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $files = File::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.files.index', compact('files'));
    }

    public function upload(Request $request)
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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Загружено файлов: {$result['success_count']}",
                    'errors' => $result['errors'],
                    'files' => $result['files']->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'original_name' => $file->original_name,
                            'size' => $file->size,
                            'mime_type' => $file->mime_type,
                            'created_at' => $file->created_at->format('d.m.Y H:i'),
                        ];
                    })
                ]);
            }

            return redirect()->route('user.files.index')
                ->with('success', "Загружено файлов: {$result['success_count']}")
                ->with('errors', $result['errors']);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->route('user.files.index')
                ->with('error', $e->getMessage());
        }
    }

    public function download(File $file)
    {
        if ($file->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        try {
            $filePath = $this->fileService->download($file);
            return response()->download($filePath, $file->original_name);
        } catch (\Exception $e) {
            return redirect()->route('user.files.index')
                ->with('error', 'Файл не найден');
        }
    }

    public function delete(File $file)
    {
        if ($file->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        try {
            $this->fileService->delete($file);
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('user.files.index')
                ->with('success', 'Файл удален');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->route('user.files.index')
                ->with('error', $e->getMessage());
        }
    }

    public function togglePublic(File $file)
    {
        if ($file->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        try {
            if ($file->is_public) {
                $file->update(['is_public' => false, 'key' => null]);
                $message = 'Файл сделан приватным';
            } else {
                $this->fileService->createPublicUrl($file);
                $message = 'Файл сделан публичным';
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_public' => $file->fresh()->is_public,
                    'public_url' => $file->fresh()->public_url
                ]);
            }

            return redirect()->route('user.files.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->route('user.files.index')
                ->with('error', $e->getMessage());
        }
    }
}
