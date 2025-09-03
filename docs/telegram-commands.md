# Команды для работы с Telegram

## Настройка вебхуков

```bash
# Установка вебхука для основного бота
php artisan telegram:webhook https://example.com --bot=bot

# Установка вебхука для админского бота
php artisan telegram:webhook https://example.com --bot=admin_bot

# Просмотр информации о вебхуке
php artisan telegram:webhook --info --bot=bot

# Удаление вебхука
php artisan telegram:webhook --remove --bot=bot
```

## Тестирование ботов

```bash
# Тест основного бота (требуется указать chat_id)
php artisan telegram:test --bot=bot --chat-id=123456789

# Тест админского бота (использует chat_id из конфига)
php artisan telegram:test --bot=admin_bot

# Тест админского бота с другим chat_id
php artisan telegram:test --bot=admin_bot --chat-id=987654321
```

### Получение chat_id

Для получения chat_id можно использовать:
1. Написать боту [@userinfobot](https://t.me/userinfobot)
2. Переслать сообщение боту [@RawDataBot](https://t.me/RawDataBot)

## Структура URL для вебхуков

- Основной бот: `/api/telegram/bot/webhook`
- Админский бот: `/api/telegram/admin_bot/webhook`
