<?php

use App\Models\User;
use App\Models\Feedback;

return [
    'entities' => [
        'users' => [
            'model' => User::class,
            'service' => \App\Services\UserService::class,
            'permissions' => ['view', 'create', 'update', 'delete'],
            'searchable' => ['name', 'email'],
            'columns' => [
                'id' => ['label' => 'ID', 'sortable' => true],
                'name' => ['label' => 'Пользователь', 'sortable' => true],
                'email' => ['label' => 'Email', 'sortable' => true],
                'role' => ['label' => 'Роль', 'type' => 'badge'],
                'telegram' => ['label' => 'Telegram', 'type' => 'badge'],
                'created_at' => ['label' => 'Дата регистрации', 'sortable' => true],
            ],
            'form_fields' => [
                'name' => ['type' => 'text', 'required' => true, 'readonly' => true],
                'email' => ['type' => 'email', 'required' => true, 'readonly' => true],
                'role' => ['type' => 'select', 'required' => true, 'options' => ['admin' => 'Администратор', 'manager' => 'Менеджер', 'user' => 'Пользователь']],
                'telegram_id' => ['type' => 'text', 'readonly' => true],
                'telegram_username' => ['type' => 'text', 'readonly' => true],
            ],
            'routes' => [
                'index' => 'admin.users.index',
                'show' => 'admin.users.show',
                'create' => 'admin.users.create',
                'edit' => 'admin.users.edit',
                'update' => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]
        ],
        'feedbacks' => [
            'model' => Feedback::class,
            'service' => \App\Services\FeedbackService::class,
            'permissions' => ['view'],
            'searchable' => ['json_data->name', 'json_data->comment'],
            'columns' => [
                'id' => ['label' => 'ID', 'sortable' => true],
                'sender' => ['label' => 'Отправитель', 'type' => 'custom'],
                'contact' => ['label' => 'Контакт', 'type' => 'custom'],
                'message' => ['label' => 'Сообщение', 'type' => 'custom'],
                'created_at' => ['label' => 'Дата отправки', 'sortable' => true],
            ],
            'form_fields' => [
                'json_data' => ['type' => 'json', 'readonly' => true],
            ],
            'routes' => [
                'index' => 'admin.feedbacks.index',
                'show' => 'admin.feedbacks.show',
            ]
        ],
    ],
    
    'navigation' => [
        'users' => [
            'label' => 'Пользователи',
            'icon' => 'fas fa-users',
            'route' => 'admin.users.index',
            'permission' => 'admin.users.view'
        ],
        'feedbacks' => [
            'label' => 'Обратная связь',
            'icon' => 'fas fa-comments',
            'route' => 'admin.feedbacks.index',
            'permission' => 'admin.feedbacks.view'
        ],
    ],
    
    'pagination' => [
        'per_page' => 15,
        'per_page_options' => [10, 15, 25, 50, 100],
    ],
    
    'search' => [
        'placeholder' => 'Поиск...',
        'debounce' => 300,
    ],
];
