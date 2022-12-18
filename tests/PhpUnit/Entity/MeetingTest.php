<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\Entity;


use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\Meeting\LimitHasBeenExceededException;
use PHPUnit\Framework\TestCase;

class MeetingTest extends TestCase
{
    public function testIsClassInstantiable()
    {
        $this->assertInstanceOf(Meeting::class, new Meeting('test', new \DateTimeImmutable()));
    }

    public function testDefaultParticipantLimit()
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());
        for ($counter = 1; $counter <= Meeting::DEFAULT_PARTICIPANT_LIMIT; $counter++) {
            $user = new User('Test');
            $meeting->addAParticipant($user);
        }
        $this->assertEquals(Meeting::DEFAULT_PARTICIPANT_LIMIT, $meeting->participants->count());
    }

    public function testExceedDefaultParticipantLimit()
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());
        $exceedLimit = Meeting::DEFAULT_PARTICIPANT_LIMIT + 1;

        $this->expectException(LimitHasBeenExceededException::class);
        $this->expectExceptionMessage('Max limit of participant has been exceeded');
        for ($counter = 1; $counter <= $exceedLimit; $counter++) {
            $user = new User('Test');
            $meeting->addAParticipant($user);
        }
    }
}
