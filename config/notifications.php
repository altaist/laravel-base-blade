<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the default notification channel that will be used
    | to send notifications when no specific channel has been specified.
    |
    */

    'default' => env('NOTIFICATION_CHANNEL', 'telegram'),

    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the notification channels for your application.
    | Each channel is configured with its own settings and can be used
    | to send different types of notifications.
    |
    */

    'channels' => [
        'telegram' => [
            'driver' => 'telegram',
        ],
    ],

];
