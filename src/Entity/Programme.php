<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: "Le nom du programme ne peut pas être vide")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date est obligatoire")]
    #[Assert\Type("\DateTimeInterface")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: "La progression ne peut pas être vide")]
    private ?string $progression = null;
    
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "L'évaluation doit être entre {{ min }} et {{ max }}"
    )]
    private ?int $evaluation = null;
    
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: "Le nombre de likes ne peut pas être négatif")]
    private ?int $likes = null;
    
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: "Le nombre de dislikes ne peut pas être négatif")]
    private ?int $dislikes = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getProgression(): ?string
    {
        return $this->progression;
    }

    public function setProgression(string $progression): self
    {
        $this->progression = $progression;
        return $this;
    }
    
    public function getEvaluation(): ?int
    {
        return $this->evaluation;
    }

    public function setEvaluation(?int $evaluation): self
    {
        $this->evaluation = $evaluation;
        return $this;
    }
    
    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }
    
    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;
        return $this;
    }

    // Helper method to calculate rating
    public function getRating(): float
    {
        if (($this->likes + $this->dislikes) === 0) {
            return 0.0;
        }
        return ($this->likes / ($this->likes + $this->dislikes)) * 5;
    }
} 