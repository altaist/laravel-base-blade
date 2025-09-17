# Руководство по миграции Telegram системы

## Обзор изменений

Система Telegram была полностью рефакторена для поддержки множественных ботов. Основные изменения:

- ✅ Новая архитектура с `TelegramBotManager` и `TelegramBot`
- ✅ Поддержка множественных ботов для одного пользователя
- ✅ Динамическая конфигурация ботов
- ✅ Организация команд по типам ботов

## Миграция данных

### 1. Запуск миграции базы данных

```bash
php artisan migrate
```

Это создаст таблицу `user_telegram_bots` для связи пользователей с ботами.

### 2. Перенос существующих telegram_id

Создайте команду для переноса данных:

```bash
php artisan make:command MigrateTelegramData
```

Содержимое команды:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserTelegramBot;
use Illuminate\Console\Command;

class MigrateTelegramData extends Command
{
    protected $signature = 'telegram:migrate-data';
    protected $description = 'Перенос существующих telegram_id в новую структуру';

    public function handle()
    {
        $this->info('Начинаем миграцию данных...');
        
        $users = User::whereNotNull('telegram_id')->get();
        $migrated = 0;
        
        foreach ($users as $user) {
            // Проверяем, не существует ли уже запись
            $exists = UserTelegramBot::where('user_id', $user->id)
                ->where('bot_name', 'main')
                ->exists();
                
            if (!$exists) {
                UserTelegramBot::create([
                    'user_id' => $user->id,
                    'bot_name' => 'main',
                    'telegram_id' => $user->telegram_id,
                    'bot_data' => [
                        'migrated_from_old_system' => true,
                        'migration_date' => now()->toISOString(),
                    ],
                ]);
                $migrated++;
            }
        }
        
        $this->info("Миграция завершена. Перенесено записей: {$migrated}");
        return Command::SUCCESS;
    }
}
```

Запустите миграцию:

```bash
php artisan telegram:migrate-data
```

### 3. Проверка миграции

```bash
# Проверить количество записей в новой таблице
php artisan tinker
>>> App\Models\UserTelegramBot::count()

# Проверить конкретного пользователя
>>> $user = App\Models\User::first()
>>> $user->telegramBots
```

## Обновление конфигурации

### 1. Переменные окружения

Убедитесь, что в `.env` настроены все необходимые переменные:

```env
# Основной бот
TELEGRAM_BOT_NAME=your_bot_name
TELEGRAM_BOT_TOKEN=your_bot_token

# Админский бот
TELEGRAM_ADMIN_BOT_NAME=your_admin_bot_name
TELEGRAM_ADMIN_BOT_TOKEN=your_admin_bot_token
TELEGRAM_ADMIN_CHAT_ID=your_admin_chat_id

# API URL (опционально)
TELEGRAM_API_URL=https://api.telegram.org
```

### 2. Конфигурация ботов

Все боты настраиваются в `config/telegram.php`. Система автоматически загружает ботов из секции `bots`.

## Тестирование

### 1. Тест конфигурации

```bash
# Очистить кэш конфигурации
php artisan config:clear

# Тест основного бота
php artisan telegram:test --bot=main --chat-id=YOUR_CHAT_ID

# Тест админского бота
php artisan telegram:test --bot=admin
```

### 2. Тест вебхуков

```bash
# Установить вебхуки
php artisan telegram:setup https://yourdomain.com

# Проверить статус
php artisan telegram:setup --info
```

### 3. Тест команд

```bash
# Запустить сервис для тестирования
php artisan telegram:run --bot=main
```

## Обратная совместимость

### Старая система продолжает работать

- ✅ Старые конфигурационные ключи (`telegram.bot`, `telegram.admin_bot`) работают
- ✅ Старое поле `telegram_id` в таблице `users` поддерживается
- ✅ Существующие команды работают без изменений

### Постепенная миграция

Вы можете мигрировать постепенно:

1. **Этап 1**: Запустить миграцию данных
2. **Этап 2**: Обновить код для использования новых методов
3. **Этап 3**: Удалить старые поля (опционально)

## Добавление новых ботов

### 1. Добавить в конфигурацию

```php
// config/telegram.php
'bots' => [
    // ... существующие боты
    'manager' => [
        'name' => env('TELEGRAM_MANAGER_BOT_NAME'),
        'token' => env('TELEGRAM_MANAGER_BOT_TOKEN'),
        'base_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
        'commands' => [
            'start' => \App\Services\Telegram\Commands\Manager\StartCommand::class,
            'stats' => \App\Services\Telegram\Commands\Manager\StatsCommand::class,
        ],
    ],
],
```

### 2. Создать команды

```bash
mkdir -p app/Services/Telegram/Commands/Manager
```

### 3. Обновить роуты

Роуты автоматически подхватывают новых ботов из конфигурации.

## Устранение неполадок

### Проблема: Бот не найден

```
Telegram bot 'manager' not found
```

**Решение**: Проверьте конфигурацию в `config/telegram.php`

### Проблема: Ошибка миграции

```
SQLSTATE[23000]: Integrity constraint violation
```

**Решение**: Проверьте уникальность `telegram_id` в таблице `user_telegram_bots`

### Проблема: Команды не работают

**Решение**: 
1. Очистите кэш: `php artisan config:clear`
2. Проверьте правильность путей к классам команд
3. Убедитесь, что команды наследуются от `BaseTelegramCommand`

## Мониторинг

### Логи

Все операции логируются в `storage/logs/telegram-*.log`:

```bash
# Просмотр логов
tail -f storage/logs/telegram-$(date +%Y-%m-%d).log
```

### Метрики

```bash
# Количество пользователей по ботам
php artisan tinker
>>> App\Models\UserTelegramBot::groupBy('bot_name')->selectRaw('bot_name, count(*) as count')->get()
```

## Заключение

Новая архитектура предоставляет:

- 🚀 **Масштабируемость** - легко добавлять новых ботов
- 🔧 **Гибкость** - настройка через конфигурацию
- 👥 **Множественные боты** - один пользователь может использовать несколько ботов
- 🔄 **Обратная совместимость** - старый код продолжает работать
- 📊 **Мониторинг** - подробное логирование и метрики
