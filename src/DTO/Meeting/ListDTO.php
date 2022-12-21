<?php
declare(strict_types=1);


namespace App\DTO\Meeting;


class ListDTO
{
    public function __construct(
        public readonly string             $name,
        public readonly \DateTimeImmutable $startTime,
        public readonly ?float             $averageRate
    )
    {
    }
}
