<?php

namespace App\Http\Controllers\Files;

use App\Enums\AttachmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteAttachmentRequest;
use App\Http\Requests\UploadAttachmentRequest;
use App\Models\Attachment;
use App\Services\Files\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    public function __construct(
        private AttachmentService $attachmentService
    ) {}

    /**
     * Загрузить attachment
     */
    public function uploadAttachment(UploadAttachmentRequest $request): JsonResponse
    {
        try {
            // Получаем связанную модель
            $relatedModel = $this->getRelatedModel($request->related_type, $request->related_id);
            
            if (!$relatedModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Связанная модель не найдена'
                ], 404);
            }

            // Создаем attachment
            $attachment = $this->attachmentService->createAttachment(
                relatedModel: $relatedModel,
                file: $request->file('file'),
                name: $request->name,
                description: $request->description,
                type: $request->type ? AttachmentType::from($request->type) : null
            );

            return response()->json([
                'success' => true,
                'attachment' => $this->formatAttachmentResponse($attachment),
                'message' => 'Attachment успешно создан'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Удалить attachment
     */
    public function deleteAttachment(Attachment $attachment, DeleteAttachmentRequest $request): JsonResponse
    {
        try {
            // Проверяем права доступа
            if (!$this->canAccessAttachment($attachment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $deleteFile = $request->boolean('delete_file', false);
            
            $this->attachmentService->deleteAttachment($attachment, $deleteFile);

            return response()->json([
                'success' => true,
                'message' => 'Attachment успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Получить attachments для модели
     */
    public function getAttachments(string $relatedType, int $relatedId, ?string $type = null): JsonResponse
    {
        try {
            $relatedModel = $this->getRelatedModel($relatedType, $relatedId);
            
            if (!$relatedModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Связанная модель не найдена'
                ], 404);
            }

            $attachmentType = $type ? AttachmentType::from($type) : null;
            $attachments = $this->attachmentService->getAttachmentsForModel($relatedModel, $attachmentType);

            return response()->json([
                'success' => true,
                'attachments' => $attachments->map(fn($attachment) => $this->formatAttachmentResponse($attachment))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Получить связанную модель
     */
    private function getRelatedModel(string $relatedType, int $relatedId): ?object
    {
        // Проверяем, что класс существует
        if (!class_exists($relatedType)) {
            return null;
        }

        // Получаем модель
        return $relatedType::find($relatedId);
    }

    /**
     * Проверить права доступа к attachment
     */
    private function canAccessAttachment(Attachment $attachment): bool
    {
        // Если attachment имеет файл, проверяем владельца файла
        if ($attachment->file && $attachment->file->user_id) {
            return $attachment->file->user_id === Auth::id();
        }

        // Для attachments без файла (только URL) разрешаем доступ всем авторизованным пользователям
        // В будущем можно добавить более сложную логику
        return Auth::check();
    }

    /**
     * Форматировать ответ с данными attachment
     */
    private function formatAttachmentResponse(Attachment $attachment): array
    {
        return [
            'id' => $attachment->id,
            'name' => $attachment->display_name,
            'description' => $attachment->description,
            'type' => $attachment->type->value,
            'type_label' => $attachment->type->getLabel(),
            'url' => $attachment->display_url,
            'file' => $attachment->file ? [
                'id' => $attachment->file->id,
                'original_name' => $attachment->file->original_name,
                'size' => $attachment->file->size,
                'mime_type' => $attachment->file->mime_type,
                'public_url' => $attachment->file->public_url,
            ] : null,
            'created_at' => $attachment->created_at->format('d.m.Y H:i'),
        ];
    }
}
