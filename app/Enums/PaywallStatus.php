<?php

namespace App\Enums;

enum PaywallStatus: string
{
    case Clear = 'clear';
    case Restricted = 'restricted';
    case Restored = 'restored';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
