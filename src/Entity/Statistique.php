<?php

namespace App\Entity;

use App\Repository\StatistiqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatistiqueRepository::class)]
class Statistique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $totalUsers;

    #[ORM\Column(type: 'integer')]
    private int $newParticipants;

    #[ORM\Column(type: 'integer')]
    private int $followUps;

    #[ORM\Column(type: 'integer')]
    private int $issues;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;
}
