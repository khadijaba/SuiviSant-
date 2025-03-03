<?php

namespace App\Entity;

use App\Repository\ProgrammeSanteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProgrammeSanteRepository::class)]
class ProgrammeSante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du programme est requis.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser 255 caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est requise.")]
    #[Assert\Length(min: 10, minMessage: "La description doit comporter au moins 15 caractères.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
    #[Assert\GreaterThan("today", message: "La date doit être supérieure à la date d'aujourd'hui.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'objectif est requis.")]
    #[Assert\Length(max: 255, maxMessage: "L'objectif ne peut pas dépasser 255 caractères.")]
    private ?string $objectif = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La localisation est requise.")]
    #[Assert\Length(max: 255, maxMessage: "La localisation ne peut pas dépasser 255 caractères.")]
    private ?string $localisation = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: "La note doit être comprise entre 0 et 5.")]
    private ?float $rating = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): static
    {
        $this->objectif = $objectif;
        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;
        return $this;
    }
}
