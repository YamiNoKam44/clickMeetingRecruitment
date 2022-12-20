<?php
declare(strict_types=1);


namespace App\Tests\PhpUnit\Service\Meeting;


use App\Entity\Meeting;
use App\Entity\User;
use App\Repository\Meeting\RateRepository;
use App\Service\Meeting\RateService;
use App\ValueObject\Meeting\MeetingRate;
use App\ValueObject\Meeting\SetRateResponse;
use Exception;
use PHPUnit\Framework\TestCase;

class RateServiceTest extends TestCase
{
    private RateRepository $rateRepository;

    protected function setUp(): void
    {
        $this->rateRepository = $this->createMock(RateRepository::class);
        parent::setUp();
    }

    public function testIsClassInstantiable(): void
    {
        $this->assertInstanceOf(RateService::class, new RateService($this->rateRepository));
    }

    public function testSuccessfulAddedRate(): void
    {
        $meetingRate = $this->premareMeetingRate();

        $this->rateRepository->method('checkExist')->willReturn(false);

        $response = (new RateService($this->rateRepository))->setRate($meetingRate);

        $this->assertInstanceOf(SetRateResponse::class, $response);
        $this->assertEquals('Rating has been added', $response->message);
        $this->assertEquals(true, $response->isAdded);
    }

    public function testCheckIsRatedReturnAlreadyAdded(): void
    {
        $meetingRate = $this->premareMeetingRate();

        $this->rateRepository->method('checkExist')->willReturn(true);
        $response = (new RateService($this->rateRepository))->setRate($meetingRate);

        $this->assertInstanceOf(SetRateResponse::class, $response);
        $this->assertEquals('You are already rated this meeting', $response->message);
        $this->assertEquals(false, $response->isAdded);
    }

    public function testIsUserTryToRateBeforeEndOfMeeting(): void
    {
        $meeting = new Meeting('test', (new \DateTimeImmutable())->modify('+1 hour'));

        $user = new User('test');
        $meeting->addAParticipant($user);

        $meetingRate = new MeetingRate($meeting, $user->id, (string)4);

        $response = (new RateService($this->rateRepository))->setRate($meetingRate);

        $this->assertInstanceOf(SetRateResponse::class, $response);
        $this->assertEquals('Meeting must be ended to able rating', $response->message);
        $this->assertEquals(false, $response->isAdded);
    }

    public function testSetRateThrowException(): void
    {
        $meetingRate = $this->premareMeetingRate();

        $this->rateRepository->method('checkExist')->willReturn(false);
        $this->rateRepository->method('add')->willThrowException(new Exception);

        $this->expectException(\Exception::class);
        (new RateService($this->rateRepository))->setRate($meetingRate);
    }

    protected function premareMeetingRate(): MeetingRate
    {
        $meeting = new Meeting('test', (new \DateTimeImmutable())->modify('-2 hour'));

        $user = new User('test');
        $meeting->addAParticipant($user);

        return new MeetingRate($meeting, $user->id, (string)4);
    }
}
