<?php
declare(strict_types=1);


namespace App\Service\Meeting;


use App\Entity\Meeting\Rate;
use App\Entity\User;
use App\Repository\Meeting\RateRepository;
use App\ValueObject\Meeting\MeetingRate;
use App\ValueObject\Meeting\SetRateResponse;

class RateService
{
    private RateRepository $rateRepository;

    public function __construct(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }

    public function setRate(MeetingRate $meetingRate): SetRateResponse
    {
        $meeting = $meetingRate->meeting;
        $user = $meeting->participants->filter(fn(User $user) => $user->id === $meetingRate->userId)->first();
        $rate = (int)$meetingRate->rate;

        $dateTimeNow = new \DateTimeImmutable('now');
        if ($meeting->endTime >= $dateTimeNow) {
            return new SetRateResponse('Meeting must be ended to able rating', false);
        }

        $isRated = $this->rateRepository->checkExist($meetingRate->meeting, $user);
        if ($isRated) {
            return new SetRateResponse('You are already rated this meeting', false);
        }

        try {
            $this->rateRepository->add(new Rate($meeting, $user, $rate));
        } catch (\Exception $exception) {
            throw $exception;
        }

        return new SetRateResponse('Rating has been added', true);
    }
}
