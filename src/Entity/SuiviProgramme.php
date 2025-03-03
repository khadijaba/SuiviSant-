<?php

namespace App\Entity;

use App\Repository\SuiviProgrammeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SuiviProgrammeRepository::class)]
class SuiviProgramme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La progression ne peut pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "La progression doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La progression ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $progression = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ProgrammeSante $programme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgression(): ?string
    {
        return $this->progression;
    }

    public function setProgression(string $progression): static
    {
        $this->progression = $progression;

        return $this;
    }

    public function getProgramme(): ?ProgrammeSante
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeSante $programme): static
    {
        $this->programme = $programme;

        return $this;
    }
}
