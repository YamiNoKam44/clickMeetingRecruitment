<?php
declare(strict_types=1);


namespace App\Entity\Meeting;


use App\Entity\Meeting;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: '`meeting_rate`')]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(length: 13)]
    public readonly string $id;

    #[ORM\ManyToOne(targetEntity: Meeting::class)]
    public readonly Meeting $meeting;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public readonly User $user;

    #[ORM\Column(type: Types::INTEGER)]
    public readonly int $rate;

    public function __construct(Meeting $meeting, User $user, int $rate)
    {
        $this->id = uniqid();
        $this->meeting = $meeting;
        $this->user = $user;
        $this->rate = $rate;
    }
}
