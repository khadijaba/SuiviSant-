<?php
namespace App\Entity;
use App\Entity\Utilisateur;
use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Rapport;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ORM\Table(name: "consultation")]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'date_consultation', type: 'datetime')]
    private ?\DateTimeInterface $dateConsultation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'consultations')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id", nullable: false)]
    private ?Utilisateur $utilisateur = null;
#[ORM\OneToOne(mappedBy: 'consultation', targetEntity: Rapport::class, cascade: ['persist', 'remove'])]
    private ?Rapport $rapport = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateConsultation(): ?\DateTimeInterface
    {
        return $this->dateConsultation;
    }

    public function setDateConsultation(?\DateTimeInterface $date): self
    {
        $this->dateConsultation = $date;
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }



 public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(?Rapport $rapport): self
    {
        $this->rapport = $rapport;

        // Synchronisation bidirectionnelle
        if ($rapport !== null && $rapport->getConsultation() !== $this) {
            $rapport->setConsultation($this);
        }

        return $this;
    }








}
