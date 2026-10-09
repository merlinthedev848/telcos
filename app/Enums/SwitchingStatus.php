<?php

namespace App\Enums;

enum SwitchingStatus: string
{
    case Draft = 'draft';
    case MatchRequested = 'match_requested';
    case Matched = 'matched';
    case SwitchOrdered = 'switch_ordered';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
