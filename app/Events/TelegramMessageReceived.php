<?php

namespace App\Events;

use App\DTOs\TelegramMessageDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelegramMessageReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly TelegramMessageDto $message
    ) {}
}
