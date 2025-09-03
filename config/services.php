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
        ],
        'admin_bot' => [
            'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'), // ID чата для админских уведомлений
        ]
    ],
];