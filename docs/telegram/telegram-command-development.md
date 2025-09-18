# Руководство по разработке Telegram команд

## Обзор

Система Telegram команд построена на базе `BaseTelegramCommand`, который предоставляет общие методы для работы с пользователями, авторизацией и отправкой сообщений.

## Создание новой команды

### 1. Создание файла команды

```bash
# Для основного бота
touch app/Services/Telegram/Commands/Main/MyCommand.php

# Для админского бота
touch app/Services/Telegram/Commands/Admin/MyCommand.php
```

### 2. Базовая структура команды

```php
<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;

class MyCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'mycommand';
    }

    public function getDescription(): string
    {
        return 'Описание команды';
    }

    public function process(TelegramMessageDto $message): void
    {
        // Логика команды
    }
}
```

### 3. Регистрация команды

Добавьте команду в `config/telegram.php`:

```php
'bots' => [
    'main' => [
        // ... другие настройки
        'commands' => [
            // ... другие команды
            'mycommand' => \App\Services\Telegram\Commands\Main\MyCommand::class,
        ],
    ],
],
```

## Работа с пользователями

### Поиск пользователя

```php
// Простой поиск (может вернуть null)
$user = $this->findUser($message);

if ($user) {
    // Пользователь найден
    $this->reply($message, "Привет, {$user->name}!");
} else {
    // Пользователь не найден
    $this->reply($message, "Пользователь не найден");
}
```

### Поиск с проверкой авторизации

```php
// Поиск с автоматической отправкой сообщения об авторизации
$user = $this->requireUser($message);
if (!$user) {
    return; // Сообщение об авторизации уже отправлено
}

// Пользователь гарантированно найден
$this->reply($message, "Привет, {$user->name}!");
```

### Обработка неавторизованных пользователей

```php
// Если нужна кастомная обработка неавторизованных пользователей
$user = $this->findUser($message);

if (!$user) {
    $this->sendUnauthorizedMessage($message);
    return;
}

// Логика для авторизованного пользователя
```

## Работа с авторизацией

### Создание ссылок

```php
// Для существующего пользователя
$authLink = $this->createAuthLink($user, $message);
$loginUrl = route('auth-link.authenticate', $authLink['token']);

// Для нового пользователя
$regLink = $this->createRegistrationLink($message);
$loginUrl = route('auth-link.authenticate', $regLink['token']);
```

### Обработка привязки аккаунта

```php
// Обработка start_param для привязки аккаунта
if (!empty($message->arguments)) {
    $this->handleAccountBinding($message);
    return;
}
```

## Отправка сообщений

### Простое сообщение

```php
$this->reply($message, "Текст сообщения");
$this->reply($message, "HTML текст", TelegramService::FORMAT_HTML);
```

### Сообщение с клавиатурой

```php
$keyboard = [
    ['Кнопка 1', 'Кнопка 2'],
    ['Кнопка 3']
];

$this->replyWithKeyboard($message, "Выберите действие:", $keyboard);
```

### Сообщение с inline клавиатурой

```php
$keyboard = [
    [['text' => 'Профиль', 'callback_data' => '/profile']],
    [['text' => 'О боте', 'callback_data' => '/about']]
];

$this->replyWithInlineKeyboard($message, "Главное меню:", $keyboard);
```

## Обработка ошибок

### Try-catch блоки

```php
try {
    // Логика команды
    $result = $this->someOperation();
    $this->reply($message, "Операция выполнена успешно");
} catch (\Exception $e) {
    Log::channel('telegram')->error('Ошибка в команде', [
        'command' => $this->getName(),
        'user_id' => $message->userId,
        'error' => $e->getMessage(),
    ]);
    
    $this->reply($message, "❌ Произошла ошибка. Попробуйте позже.");
}
```

### Валидация входных данных

```php
// Проверка аргументов команды
if (empty($message->arguments)) {
    $this->reply($message, "❌ Не указаны параметры команды");
    return;
}

$param = $message->arguments[0];
if (empty($param)) {
    $this->reply($message, "❌ Неверный параметр");
    return;
}
```

## Логирование

### Информационные сообщения

```php
Log::channel('telegram')->info('Команда выполнена', [
    'command' => $this->getName(),
    'user_id' => $message->userId,
    'bot_id' => $message->botId,
]);
```

### Ошибки

```php
Log::channel('telegram')->error('Ошибка в команде', [
    'command' => $this->getName(),
    'user_id' => $message->userId,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString(),
]);
```

## Примеры команд

### Простая информационная команда

```php
<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;

class AboutCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'about';
    }

    public function getDescription(): string
    {
        return 'Информация о боте';
    }

    public function process(TelegramMessageDto $message): void
    {
        $text = "ℹ️ <b>О боте</b>\n\n" .
            "Я помогаю управлять вашим аккаунтом.\n\n" .
            "<b>Доступные команды:</b>\n" .
            "/start - Начать работу\n" .
            "/profile - Профиль\n" .
            "/auth - Авторизация";
            
        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
```

### Команда с проверкой авторизации

```php
<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;

class ProfileCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'profile';
    }

    public function getDescription(): string
    {
        return 'Показать профиль пользователя';
    }

    public function process(TelegramMessageDto $message): void
    {
        // Требуем авторизацию
        $user = $this->requireUser($message);
        if (!$user) {
            return; // Сообщение об авторизации уже отправлено
        }

        // Показываем профиль
        $text = "👤 <b>Ваш профиль</b>\n\n" .
            "<b>Имя:</b> " . ($user->name ?? 'Не указано') . "\n" .
            "<b>Email:</b> " . ($user->email ?? 'Не указан') . "\n" .
            "<b>Роль:</b> " . ucfirst($user->role?->value ?? 'user');
            
        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
```

### Команда с inline клавиатурой

```php
<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;

class MenuCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'menu';
    }

    public function getDescription(): string
    {
        return 'Главное меню';
    }

    public function process(TelegramMessageDto $message): void
    {
        $user = $this->findUser($message);
        
        if ($user) {
            $text = "👋 Добро пожаловать, {$user->name}!\n\nВыберите действие:";
            $keyboard = [
                [['text' => '👤 Профиль', 'callback_data' => '/profile']],
                [['text' => 'ℹ️ О боте', 'callback_data' => '/about']],
                [['text' => '🔐 Авторизация', 'callback_data' => '/auth']]
            ];
        } else {
            $text = "👋 Добро пожаловать!\n\nДля начала работы необходимо авторизоваться:";
            $keyboard = [
                [['text' => '🔐 Авторизация', 'callback_data' => '/auth']],
                [['text' => 'ℹ️ О боте', 'callback_data' => '/about']]
            ];
        }
        
        $this->replyWithInlineKeyboard($message, $text, $keyboard, TelegramService::FORMAT_HTML);
    }
}
```

## Тестирование команд

### Создание тестов

```php
<?php

namespace Tests\Unit\Telegram\Commands;

use App\Services\Telegram\Commands\Main\AboutCommand;
use App\DTOs\TelegramMessageDto;
use App\Enums\TelegramMessageType;
use Tests\TestCase;

class AboutCommandTest extends TestCase
{
    public function test_command_responds_with_about_info()
    {
        $command = new AboutCommand(app('App\Services\Telegram\TelegramService'));
        
        $message = new TelegramMessageDto(
            messageType: TelegramMessageType::COMMAND,
            text: '/about',
            userId: '123456789',
            botId: 'main'
        );
        
        $command->process($message);
        
        // Проверяем, что команда обработалась без ошибок
        $this->assertTrue(true);
    }
}
```

### Тестирование с моками

```php
public function test_command_requires_user_authorization()
{
    $telegramService = $this->createMock('App\Services\Telegram\TelegramService');
    $command = new ProfileCommand($telegramService);
    
    $message = new TelegramMessageDto(
        messageType: TelegramMessageType::COMMAND,
        text: '/profile',
        userId: '123456789',
        botId: 'main'
    );
    
    // Мокаем отправку сообщения
    $telegramService->expects($this->once())
        ->method('sendMessageToUser');
    
    $command->process($message);
}
```

## Лучшие практики

### 1. Всегда проверяйте авторизацию

```php
// Хорошо
$user = $this->requireUser($message);
if (!$user) return;

// Плохо - может привести к ошибкам
$user = $this->findUser($message);
// Используем $user без проверки
```

### 2. Используйте типизацию

```php
// Хорошо
public function process(TelegramMessageDto $message): void

// Плохо
public function process($message)
```

### 3. Логируйте важные события

```php
Log::channel('telegram')->info('Команда выполнена', [
    'command' => $this->getName(),
    'user_id' => $message->userId,
]);
```

### 4. Обрабатывайте ошибки

```php
try {
    // Логика команды
} catch (\Exception $e) {
    Log::channel('telegram')->error('Ошибка в команде', [
        'error' => $e->getMessage(),
    ]);
    
    $this->reply($message, "❌ Произошла ошибка");
}
```

### 5. Используйте константы для форматов

```php
// Хорошо
$this->reply($message, $text, TelegramService::FORMAT_HTML);

// Плохо
$this->reply($message, $text, 'HTML');
```

## Отладка

### Включение подробного логирования

```php
// В .env
LOG_LEVEL=debug

// В команде
Log::channel('telegram')->debug('Отладочная информация', [
    'data' => $someData,
]);
```

### Проверка работы команд

```bash
# Запуск сервиса для тестирования
php artisan telegram:run --bot=main --interval=1

# Просмотр логов
tail -f storage/logs/telegram-$(date +%Y-%m-%d).log
```

## Заключение

Следуя этому руководству, вы сможете создавать эффективные и надежные Telegram команды, которые легко поддерживать и расширять.
