<?php
declare(strict_types=1);


namespace App\Enum\Meeting;


enum Filters: string
{
    case Status = 'status';

    public static function allStatus(): array
    {
        return array_column(self::cases(), 'value');
    }
}
