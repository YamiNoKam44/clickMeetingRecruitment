<?php

namespace App\Repository;

use App\Entity\Meeting;
use App\Entity\Meeting\Rate;
use App\Enum\Meeting\Filters;
use App\Enum\Meeting\OrderBy;
use App\Enum\Meeting\Status;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class MeetingRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Meeting $newMeeting): void
    {
        $this->entityManager->persist($newMeeting);
        $this->entityManager->flush();
    }

    public function get(string $meetingId): Meeting
    {
        return $this->entityManager->getRepository(Meeting::class)->find($meetingId);
    }

    public function getWithParticipant(string $meetingId): ?Meeting
    {
        return $this->entityManager->getRepository(Meeting::class)->createQueryBuilder('meeting')
            ->select('meeting', 'participants')
            ->leftJoin('meeting.participants', 'participants')
            ->where('meeting.id = :meetingId')
            ->setParameter('meetingId', $meetingId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(Meeting::class)->findAll();
    }

    public function findAllWithFilers(string $orderBy, string $orderDirection, ArrayCollection $filters): array
    {
        $queryBuilder = $this->entityManager->getRepository(Meeting::class)->createQueryBuilder('meeting');

        $queryBuilder->select('meeting.name', 'meeting.startTime', 'avg(rate.rate) as averageRate')
            ->leftJoin(Rate::class, 'rate', Join::WITH, 'rate.meeting = meeting.id');

        $this->setUpFilters($filters, $queryBuilder);
        $this->setUpOrderBy($orderBy, $queryBuilder, $orderDirection);

        return $queryBuilder
            ->groupBy('meeting.name', 'meeting.startTime')
            ->getQuery()
            ->getResult();
    }

    private function setUpOrderBy(string $orderBy, QueryBuilder $queryBuilder, string $orderDirection): void
    {
        if ($orderBy === OrderBy::StartTime->value) {
            $queryBuilder
                ->orderBy('meeting.startTime', $orderDirection);
        }
    }

    private function setUpFilters(ArrayCollection $filters, QueryBuilder $queryBuilder): void
    {
        if ($filters->get(Filters::Status->value)) {
            switch (strtoupper($filters->get(Filters::Status->value))) {
                case Status::DONE->name:
                    $dateTimeNow = new \DateTimeImmutable();
                    $queryBuilder
                        ->andWhere('meeting.endTime <= :now')
                        ->setParameter('now', $dateTimeNow);
                    break;
                case Status::IN_SESSION->name:
                    $dateTimeNow = new \DateTimeImmutable();
                    $queryBuilder
                        ->andWhere('meeting.startTime >= :now AND meeting.endTime <= :now')
                        ->setParameter('now', $dateTimeNow);
                    break;
                case Status::FULL->name:
                    $queryBuilder
                        ->addSelect('count(participants.id) AS participantsCount')
                        ->leftJoin('meeting.participants', 'participants')
                        ->andHaving('participantsCount = :participantsCount')
                        ->setParameter('participantsCount', Meeting::DEFAULT_PARTICIPANT_LIMIT);
                    break;
                case Status::OPEN_TO_REGISTRATION->name:
                    $dateTimeNow = new \DateTimeImmutable();
                    $queryBuilder
                        ->addSelect('count(participants.id) AS participantsCount')
                        ->leftJoin('meeting.participants', 'participants')
                        ->andWhere('meeting.startTime >= :now')
                        ->setParameter('now', $dateTimeNow)
                        ->andHaving('participantsCount < :participantsCount')
                        ->setParameter('participantsCount', Meeting::DEFAULT_PARTICIPANT_LIMIT);
                    break;
                default:
                    break;
            }
        }
    }
}

