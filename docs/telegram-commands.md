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
php artisan telegram:test --bot=bot --chat-id=123456789

# Тест админского бота (использует chat_id из конфига)
php artisan telegram:test --bot=admin_bot

# Тест админского бота с другим chat_id
php artisan telegram:test --bot=admin_bot --chat-id=987654321
```

## Сервис для получения сообщений

```bash
# Запуск сервиса для основного бота (интервал 5 секунд)
php artisan telegram:run

# Запуск сервиса для админского бота
php artisan telegram:run --bot=admin_bot

# Настройка интервала (например, 10 секунд)
php artisan telegram:run --interval=10

# Комбинированные параметры
php artisan telegram:run --bot=admin_bot --interval=3
```

**Примечание:** Сервис работает в бесконечном цикле. Для остановки нажмите Ctrl+C.

### Получение chat_id

Для получения chat_id можно использовать:
1. Написать боту [@userinfobot](https://t.me/userinfobot)
2. Переслать сообщение боту [@RawDataBot](https://t.me/RawDataBot)

## Команды по умолчанию

Бот автоматически отвечает на обычные текстовые сообщения (не команды) с помощью команды по умолчанию, которая отправляет дружелюбное сообщение и предлагает использовать `/about` для получения списка доступных команд.

## Структура URL для вебхуков

- Основной бот: `/api/telegram/bot/webhook`
- Админский бот: `/api/telegram/admin_bot/webhook`
