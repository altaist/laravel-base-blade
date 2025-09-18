<?php

namespace App\Providers;

use App\Contracts\GptTransportInterface;
use App\Services\AI\GptTransportService;
use Illuminate\Support\ServiceProvider;

class GptServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GptTransportInterface::class, function ($app) {
            return new GptTransportService(
                apiKey: config('services.openai.key'),
                apiUrl: config('services.openai.url'),
                proxyUrl: config('services.openai.proxy_url'),
                defaultModel: config('services.openai.default_model'),
                defaultTemperature: config('services.openai.default_temperature'),
                defaultMaxTokens: config('services.openai.default_max_tokens'),
            );
        });
    }
}