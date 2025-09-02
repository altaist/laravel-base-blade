<?php

namespace App\Enums;

enum TelegramMessageType: string
{
    case TEXT = 'text';
    case COMMAND = 'command';
    case SYSTEM = 'system';
    case PHOTO = 'photo';
    case DOCUMENT = 'document';
    case CALLBACK_QUERY = 'callback_query';
}