<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\Factory\Meeting;


use App\DTO\Meeting\MeetingDTO;
use App\Entity\Meeting;
use App\Entity\User;
use App\Enum\Meeting\Status;
use App\Factory\Meeting\MeetingFactory;
use PHPUnit\Framework\TestCase;

class MeetingFactoryTest extends TestCase
{
    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(MeetingFactory::class, new MeetingFactory());
    }

    public function testMeetingWithStatusFull(): void
    {
        $dateMeetingNotStarted = (new \DateTimeImmutable())->modify('+1 day');
        $meeting = new Meeting('test', $dateMeetingNotStarted);
        for ($counter = 1; $counter <= Meeting::DEFAULT_PARTICIPANT_LIMIT; $counter++) {
            $user = new User('Test');
            $meeting->addAParticipant($user);
        }
        $meetingDTO = MeetingFactory::createWithStatus($meeting);

        $this->assertInstanceOf(MeetingDTO::class, $meetingDTO);
        $this->assertEquals(Status::FULL, $meetingDTO->status);
    }
    public function testMeetingWithStatusInSession(): void
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());
        $meetingDTO = MeetingFactory::createWithStatus($meeting);

        $this->assertInstanceOf(MeetingDTO::class, $meetingDTO);
        $this->assertEquals(Status::IN_SESSION, $meetingDTO->status);
    }
    public function testMeetingWithStatusDone(): void
    {
        $dateMeetingFinished = (new \DateTimeImmutable())->modify('-1 day');
        $meeting = new Meeting('test', $dateMeetingFinished);
        $meetingDTO = MeetingFactory::createWithStatus($meeting);

        $this->assertInstanceOf(MeetingDTO::class, $meetingDTO);
        $this->assertEquals(Status::DONE, $meetingDTO->status);
    }

    public function testMeetingWithStatusOpen(): void
    {
        $dateMeetingNotStarted = (new \DateTimeImmutable())->modify('+1 day');
        $meeting = new Meeting('test', $dateMeetingNotStarted);
        $meetingDTO = MeetingFactory::createWithStatus($meeting);

        $this->assertInstanceOf(MeetingDTO::class, $meetingDTO);
        $this->assertEquals(Status::OPEN_TO_REGISTRATION, $meetingDTO->status);
    }
}
