<?php

namespace App\Enums;

enum GptModelType: string
{
    case GPT_4_TURBO = 'gpt-4-turbo-preview';
    case GPT_4 = 'gpt-4';
    case GPT_3_5_TURBO = 'gpt-3.5-turbo';
    case WHISPER = 'whisper-1';
    case DALL_E = 'dall-e-3';
}
