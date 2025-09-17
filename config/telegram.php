<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Конфигурация ботов
    |--------------------------------------------------------------------------
    |
    | Настройки всех Telegram ботов в системе
    |
    */
    'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),

    'bots' => [
        'main' => [
            'name' => env('TELEGRAM_BOT_NAME'),
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'base_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\Main\StartCommand::class,
                'auth' => \App\Services\Telegram\Commands\Main\AuthLinkCommand::class,
                'profile' => \App\Services\Telegram\Commands\Main\ProfileCommand::class,
                'about' => \App\Services\Telegram\Commands\Main\AboutCommand::class,
                'default' => \App\Services\Telegram\Commands\Main\DefaultCommand::class,
            ],
        ],
        'admin' => [
            'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
            'base_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\Admin\AdminUsersCommand::class,
                'users' => \App\Services\Telegram\Commands\Admin\AdminUsersCommand::class,
                'about' => \App\Services\Telegram\Commands\Admin\AdminAboutCommand::class,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Обратная совместимость
    |--------------------------------------------------------------------------
    |
    | Старые ключи для обратной совместимости
    |
    */
    'bot' => [
        'name' => env('TELEGRAM_BOT_NAME'),
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'commands' => [
            'start' => \App\Services\Telegram\Commands\Main\StartCommand::class,
            'auth' => \App\Services\Telegram\Commands\Main\AuthLinkCommand::class,
            'profile' => \App\Services\Telegram\Commands\Main\ProfileCommand::class,
            'about' => \App\Services\Telegram\Commands\Main\AboutCommand::class,
            'default' => \App\Services\Telegram\Commands\Main\DefaultCommand::class,
        ],
    ],
    'admin_bot' => [
        'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
        'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
        'commands' => [
            'start' => \App\Services\Telegram\Commands\Admin\AdminUsersCommand::class,
            'users' => \App\Services\Telegram\Commands\Admin\AdminUsersCommand::class,
            'about' => \App\Services\Telegram\Commands\Admin\AdminAboutCommand::class,
        ],
    ],
];
