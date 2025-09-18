<?php

namespace App\Contracts;

use App\DTOs\GptRequestDto;

interface GptTransportInterface
{
    /**
     * Отправляет запрос на генерацию текста
     */
    public function requestText(GptRequestDto $request): GptRequestDto;

    /**
     * Отправляет запрос на генерацию изображения
     */
    public function requestImg(GptRequestDto $request): GptRequestDto;

    /**
     * Отправляет запрос на обработку аудио
     */
    public function requestAudio(GptRequestDto $request): GptRequestDto;
}