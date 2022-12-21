<?php
declare(strict_types=1);


namespace App\Service\Meeting;


use App\DTO\Meeting\ListDTO;
use App\Repository\MeetingRepository;
use App\ValueObject\Meeting\ListOrderAndFilters;
use Doctrine\Common\Collections\ArrayCollection;

class MeetingService
{
    private MeetingRepository $meetingRepository;

    public function __construct(MeetingRepository $meetingRepository)
    {
        $this->meetingRepository = $meetingRepository;
    }

    public function fetchList(ListOrderAndFilters $orderAndFilters): ArrayCollection
    {
        $meetingList = $this->meetingRepository->findAllWithFilers($orderAndFilters->orderBy, $orderAndFilters->orderDirection,
            $orderAndFilters->filters);

        $meetingDTOCollection = new ArrayCollection();
        foreach ($meetingList as $meeting) {
            $meetingDTOCollection->add(new ListDTO($meeting['name'], $meeting['startTime'],
                $meeting['averageRate'] ? round((float)$meeting['averageRate'], 2) : null));
        }

        return $meetingDTOCollection;
    }
}
