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
    'bots' => [
        'main' => [
            'name' => env('TELEGRAM_BOT_NAME'),
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\StartCommand::class,
                'auth' => \App\Services\Telegram\Commands\AuthLinkCommand::class,
                'profile' => \App\Services\Telegram\Commands\ProfileCommand::class,
                'about' => \App\Services\Telegram\Commands\AboutCommand::class,
                'default' => \App\Services\Telegram\Commands\DefaultCommand::class,
            ],
        ],
        'admin' => [
            'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\AdminUsersCommand::class,
                'users' => \App\Services\Telegram\Commands\AdminUsersCommand::class,
                'about' => \App\Services\Telegram\Commands\AdminAboutCommand::class,
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
            'start' => \App\Services\Telegram\Commands\StartCommand::class,
            'auth' => \App\Services\Telegram\Commands\AuthLinkCommand::class,
            'profile' => \App\Services\Telegram\Commands\ProfileCommand::class,
            'about' => \App\Services\Telegram\Commands\AboutCommand::class,
            'default' => \App\Services\Telegram\Commands\DefaultCommand::class,
        ],
    ],
    'admin_bot' => [
        'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
        'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
        'commands' => [
            'start' => \App\Services\Telegram\Commands\AdminUsersCommand::class,
            'users' => \App\Services\Telegram\Commands\AdminUsersCommand::class,
            'about' => \App\Services\Telegram\Commands\AdminAboutCommand::class,
        ],
    ],
];
