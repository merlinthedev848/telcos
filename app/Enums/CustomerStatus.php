<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case Lead = 'lead';
    case Active = 'active';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
