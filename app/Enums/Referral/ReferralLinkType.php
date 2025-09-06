<?php

namespace App\Enums\Referral;

enum ReferralLinkType: string
{
    case SOCIAL = 'social';        // Социальные сети
    case MESSENGER = 'messenger';  // Мессенджеры
    case OFFLINE = 'offline';      // Офлайн приглашения
    case CUSTOM = 'custom';        // Пользовательские

    /**
     * Получить человекочитаемое название типа
     */
    public function getLabel(): string
    {
        return match($this) {
            self::SOCIAL => 'Социальные сети',
            self::MESSENGER => 'Мессенджеры',
            self::OFFLINE => 'Офлайн приглашения',
            self::CUSTOM => 'Пользовательские',
        };
    }

    /**
     * Получить все типы с их названиями
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }
}
