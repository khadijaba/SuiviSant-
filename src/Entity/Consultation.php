<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de la consultation ne peut pas être vide.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
    #[Assert\LessThanOrEqual(
        "today",
        message: "La date de la consultation ne peut pas être dans le futur."
    )]
    
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: "La description de la consultation ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    // Définition de la relation One-to-One avec l'entité Rapport
    #[ORM\OneToOne(targetEntity: Rapport::class, mappedBy: 'consultation', cascade: [ 'remove'])]
    #[Assert\Valid] // Valide l'objet Rapport associé si nécessaire
    private ?Rapport $rapport = null;

   

    // Getter et Setter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour la date
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface|string|null $date): static
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        $this->date = $date;
        return $this;
    }

    // Getter et Setter pour la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Getter et Setter pour la relation avec Rapport
    public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(Rapport $rapport): static
    {
        $this->rapport = $rapport;

        // Assurer la relation inverse avec la consultation
        if ($rapport->getConsultation() !== $this) {
            $rapport->setConsultation($this);
        }

        return $this;
    }

    // Méthode pour dissocier le rapport
    public function removeRapport(): static
    {
        if ($this->rapport !== null) {
            // Supprimer la relation inverse
            $this->rapport->setConsultation(null);
            $this->rapport = null;
        }
        return $this;
    }
}

