<?php
declare(strict_types=1);


namespace App\ValueObject\Meeting;


use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\Meeting\RatingValidateException;
use App\ValueObject\Validate;

class MeetingRate implements Validate
{
    public function __construct(
        public readonly ?Meeting $meeting,
        public readonly ?string  $userId,
        public readonly ?string  $rate,
    )
    {
    }

    public function validate(): void
    {
        if ($this->meeting === null) {
            throw new RatingValidateException('Meeting not found');
        }

        if ($this->userId === null || $this->meeting->participants->filter(fn(User $user) => $user->id === $this->userId)->isEmpty() === true) {
            throw new RatingValidateException('You are not participants of the meeting');
        }

        if ($this->rate === null || $this->rate != (int)$this->rate) {
            throw new RatingValidateException('Wrong rate');
        }

        if ($this->rate < 1 || $this->rate > 5) {
            throw new RatingValidateException('Rate between 1-5');
        }

    }
}
