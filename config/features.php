<?php

return [
    'auto_auth' => [
        'enabled' => env('FEATURE_AUTO_AUTH_ENABLED', true),
        'expires_days' => env('FEATURE_AUTO_AUTH_EXPIRES_DAYS', 30),
        'rate_limit' => env('FEATURE_AUTO_AUTH_RATE_LIMIT', 10),
    ],
    
    'telegram_bot' => [
        'enabled' => env('FEATURE_TELEGRAM_BOT_ENABLED', true),
    ],
    
    'referral_system' => [
        'enabled' => env('FEATURE_REFERRAL_ENABLED', true),
    ],
];
