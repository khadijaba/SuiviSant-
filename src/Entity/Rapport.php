<?php

namespace App\Entity;

use App\Repository\RapportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RapportRepository::class)]
class Rapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date ne peut pas être vide.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide.")]
    #[Assert\LessThanOrEqual(
        "today",
        message: "La date du rapport ne peut pas être dans le futur."
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    // Nouvelle propriété 'contenu' ajoutée avec validation
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank(message: "Le contenu du rapport ne peut pas être vide.")]
    #[Assert\Length(
        min: 30,
        max: 1000,
        minMessage: "Le contenu doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le contenu ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $contenu = null;


    #[ORM\OneToOne(inversedBy: 'rapport', targetEntity: Consultation::class, cascade: ['remove'])]
#[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
private ?Consultation $consultation = null;

    // Getter et Setter pour la propriété 'id'
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour la propriété 'date'
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    // Getter et Setter pour la propriété 'description'
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Getter et Setter pour la propriété 'contenu'
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    // Getter et Setter pour la propriété 'consultation'
    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): static
    {
        $this->consultation = $consultation;

        // Assurer la relation inverse avec la consultation
        if ($consultation !== null && $consultation->getRapport() !== $this) {
            $consultation->setRapport($this);
        }

        return $this;
    }

  
    
  
    
}
