# Команды для работы с Telegram

## Настройка вебхуков

```bash
# Установка вебхуков для всех ботов
php artisan telegram:setup https://example.com

# Просмотр информации о всех вебхуках
php artisan telegram:setup --info

# Удаление всех вебхуков
php artisan telegram:setup --remove
```

## Тестирование ботов

```bash
# Тест основного бота (требуется указать chat_id)
php artisan telegram:test --bot=main --chat-id=123456789

# Тест админского бота (использует chat_id из конфига)
php artisan telegram:test --bot=admin

# Тест админского бота с другим chat_id
php artisan telegram:test --bot=admin --chat-id=987654321

# Тест менеджерского бота
php artisan telegram:test --bot=manager --chat-id=123456789
```

## Сервис для получения сообщений

```bash
# Запуск сервиса для основного бота (интервал 5 секунд)
php artisan telegram:run

# Запуск сервиса для админского бота
php artisan telegram:run --bot=admin

# Запуск сервиса для менеджерского бота
php artisan telegram:run --bot=manager

# Настройка интервала (например, 10 секунд)
php artisan telegram:run --interval=10

# Комбинированные параметры
php artisan telegram:run --bot=admin --interval=3
```

**Примечание:** Сервис работает в бесконечном цикле. Для остановки нажмите Ctrl+C.

### Получение chat_id

Для получения chat_id можно использовать:
1. Написать боту [@userinfobot](https://t.me/userinfobot)
2. Переслать сообщение боту [@RawDataBot](https://t.me/RawDataBot)

## Команды по умолчанию

Бот автоматически отвечает на обычные текстовые сообщения (не команды) с помощью команды по умолчанию, которая отправляет дружелюбное сообщение и предлагает использовать `/about` для получения списка доступных команд.

## Структура URL для вебхуков

- Основной бот: `/api/telegram/main/webhook`
- Админский бот: `/api/telegram/admin/webhook`
- Менеджерский бот: `/api/telegram/manager/webhook`

## Новая архитектура

Система теперь поддерживает множественных ботов с гибкой конфигурацией:

### Доступные боты
- **main** - Основной бот для пользователей
- **admin** - Админский бот для администраторов
- **manager** - Менеджерский бот для менеджеров

### Конфигурация ботов

Все боты настраиваются в `config/telegram.php`:

```php
'bots' => [
    'main' => [
        'name' => env('TELEGRAM_BOT_NAME'),
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'base_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
        'commands' => [
            'start' => \App\Services\Telegram\Commands\Main\StartCommand::class,
            'profile' => \App\Services\Telegram\Commands\Main\ProfileCommand::class,
            'about' => \App\Services\Telegram\Commands\Main\AboutCommand::class,
            'auth' => \App\Services\Telegram\Commands\Main\AuthLinkCommand::class,
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
```

### Множественные боты для пользователей

Пользователи теперь могут быть привязаны к нескольким ботам одновременно через таблицу `user_telegram_bots`:

```php
// Получить Telegram ID пользователя для конкретного бота
$telegramId = $user->getTelegramIdForBot('main');

// Проверить, привязан ли пользователь к боту
$hasBot = $user->hasTelegramBot('admin');

// Получить все боты пользователя
$userBots = $user->telegramBots;
```
