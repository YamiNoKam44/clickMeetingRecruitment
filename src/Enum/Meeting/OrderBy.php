<?php
declare(strict_types=1);


namespace App\Enum\Meeting;


Enum OrderBy: string
{
    case StartTime = 'startTime';

    public static function allStatus(): array
    {
        return array_column(self::cases(), 'value');
    }
}
