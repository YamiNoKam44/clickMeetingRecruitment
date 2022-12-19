<?php
declare(strict_types=1);


namespace App\DTO\Meeting;


use App\Enum\Meeting\Status;
use Doctrine\Common\Collections\Collection;

class MeetingDTO
{

    public function __construct(
        public readonly string             $id,
        public readonly string             $name,
        public readonly \DateTimeImmutable $startTime,
        public readonly \DateTimeImmutable $endTime,
        public readonly Collection         $participants,
        public readonly Status             $status
    )
    {
    }
}