<?php

namespace App\DTOs;

class GptRequestDto
{
    public function __construct(
        public readonly string $prompt,
        public readonly ?string $response = null,
        public readonly ?string $relatedObjectType = null,
        public readonly ?int $relatedObjectId = null,
        public readonly array $options = [],
        public readonly ?string $model = null,
        public readonly ?float $temperature = null,
        public readonly ?int $maxTokens = null,
    ) {}

    /**
     * Создает новый экземпляр DTO с обновленным ответом
     */
    public function withResponse(string $response): self
    {
        return new self(
            prompt: $this->prompt,
            response: $response,
            relatedObjectType: $this->relatedObjectType,
            relatedObjectId: $this->relatedObjectId,
            options: $this->options,
            model: $this->model,
            temperature: $this->temperature,
            maxTokens: $this->maxTokens,
        );
    }

    /**
     * Создает новый экземпляр DTO с обновленными опциями
     */
    public function withOptions(array $options): self
    {
        return new self(
            prompt: $this->prompt,
            response: $this->response,
            relatedObjectType: $this->relatedObjectType,
            relatedObjectId: $this->relatedObjectId,
            options: array_merge($this->options, $options),
            model: $this->model,
            temperature: $this->temperature,
            maxTokens: $this->maxTokens,
        );
    }
}
