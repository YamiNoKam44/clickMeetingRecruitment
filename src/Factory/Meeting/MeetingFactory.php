<?php
declare(strict_types=1);


namespace App\Factory\Meeting;


use App\DTO\Meeting\MeetingDTO;
use App\Entity\Meeting;
use App\Enum\Meeting\Status;

class MeetingFactory
{
    public static function createWithStatus(Meeting $meeting): MeetingDTO
    {
        $dateTimeNow = new \DateTimeImmutable();
        $status = Status::OPEN_TO_REGISTRATION;
        switch ($meeting) {
            case $meeting->endTime <= $dateTimeNow:
                $status = Status::DONE;
                break;
            case $meeting->endTime >= $dateTimeNow && $meeting->startTime <= $dateTimeNow:
                $status = Status::IN_SESSION;
                break;
            case $meeting->participants->count() >= Meeting::DEFAULT_PARTICIPANT_LIMIT:
                $status = Status::FULL;
                break;
        }

        return new MeetingDTO($meeting->id, $meeting->name, $meeting->startTime, $meeting->endTime,
            $meeting->participants, $status);
    }
}
