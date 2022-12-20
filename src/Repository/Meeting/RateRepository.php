<?php
declare(strict_types=1);


namespace App\Repository\Meeting;


use App\Entity\Meeting;
use App\Entity\Meeting\Rate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RateRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Rate $newRate): void
    {
        $this->entityManager->persist($newRate);
        $this->entityManager->flush();
    }

    public function checkExist(Meeting $meeting, User $user): bool
    {
        return (bool)$this->entityManager->getRepository(Rate::class)->createQueryBuilder('rate')
            ->select('1 as check')
            ->where('rate.meeting = :meeting')
            ->andWhere('rate.user = :user')
            ->setParameters([
                'meeting' => $meeting,
                'user' => $user
            ])
            ->getQuery()
            ->getResult();
    }
}
