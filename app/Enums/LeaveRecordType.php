<?php

namespace App\Enums;

enum LeaveRecordType: string
{
    case ANNUAL = 'annual';
    case SICK = 'sick';
    case UNPAID = 'unpaid';
    case PRIVILEGE = 'privilege';
    case FLEXI = 'flexi';
    case PUBLIC_HOLIDAY = 'public_holiday';

    /**
     * Get all enum values as an array of strings.
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
