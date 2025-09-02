<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'bot' => [
            'name' => env('TELEGRAM_BOT_NAME'),
            'token' => env('TELEGRAM_BOT_TOKEN'),
        ]
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
        'proxy_url' => env('GPT_PROXY_URL'),
        'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4-turbo-preview'),
        'default_temperature' => env('OPENAI_DEFAULT_TEMPERATURE', 0.7),
        'default_max_tokens' => env('OPENAI_DEFAULT_MAX_TOKENS', 2000),
    ],
];