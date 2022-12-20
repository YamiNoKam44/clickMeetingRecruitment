<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\ValueObject\Meeting;


use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\Meeting\RatingValidateException;
use App\ValueObject\Meeting\MeetingRate;
use PHPUnit\Framework\TestCase;

class MeetingRateTest extends TestCase
{
    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(MeetingRate::class, new MeetingRate(new Meeting('test', new \DateTimeImmutable()), 'test', (string)4));
    }

    public function testValidationSuccess(): void
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());

        $user = new User('test');
        $meeting->addAParticipant($user);

        $meetingRate = new MeetingRate($meeting, $user->id, (string)4);
        $this->assertNull($meetingRate->validate());
    }

    public function testValidationThrowMeetingNotFound()
    {
        $meetingRate = new MeetingRate(null, null, (string)4);

        $this->expectException(RatingValidateException::class);
        $this->expectExceptionMessage('Meeting not found');
        $meetingRate->validate();
    }
    public function testValidationThrowYouAreNotParticipants()
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());

        $user = new User('test');
        $meeting->addAParticipant($user);
        $meetingRate = new MeetingRate($meeting, 'test', (string)4);

        $this->expectException(RatingValidateException::class);
        $this->expectExceptionMessage('You are not participants of the meeting');
        $meetingRate->validate();
    }
    public function testValidationThrowWrongRate()
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());

        $user = new User('test');
        $meeting->addAParticipant($user);

        $meetingRate = new MeetingRate($meeting, $user->id, 'test');

        $this->expectException(RatingValidateException::class);
        $this->expectExceptionMessage('Wrong rate');
        $meetingRate->validate();
    }
    public function testValidationThrowMeetingRateBetween()
    {
        $meeting = new Meeting('test', new \DateTimeImmutable());

        $user = new User('test');
        $meeting->addAParticipant($user);

        $meetingRate = new MeetingRate($meeting, $user->id, (string)6);

        $this->expectException(RatingValidateException::class);
        $this->expectExceptionMessage('Rate between 1-5');
        $meetingRate->validate();
    }
}
