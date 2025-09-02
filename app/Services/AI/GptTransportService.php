<?php

namespace App\Services\AI;

use App\Contracts\GptTransportInterface;
use App\DTOs\GptRequestDto;
use RuntimeException;
use Illuminate\Support\Facades\Http;

class GptTransportService implements GptTransportInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiUrl = 'https://api.openai.com/v1',
        private readonly ?string $proxyUrl = null,
        private readonly string $defaultModel = 'gpt-4-turbo-preview',
        private readonly float $defaultTemperature = 0.7,
        private readonly int $defaultMaxTokens = 2000,
    ) {}

    private function makeRequest(): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ]);

        if ($this->proxyUrl) {
            $request->withOptions(['proxy' => $this->proxyUrl]);
        }

        return $request;
    }

    public function requestText(GptRequestDto $request): GptRequestDto
    {
        return $this->processRequest($request);
    }

    public function requestImg(GptRequestDto $request): GptRequestDto
    {
        try {
            $response = $this->makeRequest()
                ->post("{$this->apiUrl}/images/generations", [
                    'prompt' => $request->prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                    ...$request->options,
                ]);

            if (!$response->successful()) {
                throw new RuntimeException("API вернул ошибку: " . $response->body());
            }

            $data = $response->json();
            return $request->withResponse($data['data'][0]['url']);
        } catch (\Exception $e) {
            throw new RuntimeException("Ошибка при генерации изображения: {$e->getMessage()}");
        }
    }

    public function requestAudio(GptRequestDto $request): GptRequestDto
    {
        try {
            // Для аудио используем только Authorization header
            $httpRequest = Http::withHeaders(['Authorization' => "Bearer {$this->apiKey}"]);
            
            if ($this->proxyUrl) {
                $httpRequest->withOptions(['proxy' => $this->proxyUrl]);
            }

            $response = $httpRequest->attach(
                'file', 
                file_get_contents($request->prompt), 
                'audio.mp3'
            )->post("{$this->apiUrl}/audio/transcriptions", [
                'model' => 'whisper-1',
                ...$request->options,
            ]);

            if (!$response->successful()) {
                throw new RuntimeException("API вернул ошибку: " . $response->body());
            }

            $data = $response->json();
            return $request->withResponse($data['text']);
        } catch (\Exception $e) {
            throw new RuntimeException("Ошибка при обработке аудио: {$e->getMessage()}");
        }
    }

    private function processRequest(GptRequestDto $request): GptRequestDto
    {
        try {
            $response = $this->makeRequest()
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => $request->model ?? $this->defaultModel,
                    'temperature' => $request->temperature ?? $this->defaultTemperature,
                    'max_tokens' => $request->maxTokens ?? $this->defaultMaxTokens,
                    'messages' => [
                        ['role' => 'user', 'content' => $request->prompt],
                    ],
                    ...$request->options,
                ]);

            if (!$response->successful()) {
                throw new RuntimeException("API вернул ошибку: " . $response->body());
            }

            $data = $response->json();
            return $request->withResponse($data['choices'][0]['message']['content']);
        } catch (\Exception $e) {
            throw new RuntimeException("Ошибка при обработке запроса: {$e->getMessage()}");
        }
    }

    public function checkApiAvailability(): bool
    {
        try {
            $response = $this->makeRequest()
                ->get("{$this->apiUrl}/models");

            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }
}