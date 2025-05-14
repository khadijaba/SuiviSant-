<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    

    #[ORM\Column(type: "string", length: 8, nullable: false)]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: "/^\d{8}$/",
        message: "Phone number must be exactly 8 digits"
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email address')]
    private ?string $email = null;

    #[ORM\Column(length: 500)]
    #[Assert\Length(
        max: 500,
        maxMessage: 'Message should not be longer than {{ limit }} characters'
    )]
    private ?string $message = null;

    // Added DispoExpert field for relation
    #[ORM\OneToOne(inversedBy: 'rendezVous', targetEntity: DispoExpert::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "dispo_expert_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?DispoExpert $dispoExpert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getDispoExpert(): ?DispoExpert
    {
        return $this->dispoExpert;
    }

    public function setDispoExpert(?DispoExpert $dispoExpert): static
    {
        $this->dispoExpert = $dispoExpert;
        if ($dispoExpert !== null && $dispoExpert->getRendezVous() !== $this) {
            $dispoExpert->setRendezVous($this);
        }
        return $this;
    }
}
