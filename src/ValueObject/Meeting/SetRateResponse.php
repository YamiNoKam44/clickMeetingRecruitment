<?php
declare(strict_types=1);


namespace App\ValueObject\Meeting;


class SetRateResponse
{
    public function __construct(
        public readonly string $message,
        public readonly bool $isAdded
    )
    {
    }
}
