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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)] // Set nullable to true
    #[Assert\NotBlank(message: 'Date is required')]
    #[Assert\Type("\DateTimeInterface", message: 'Please enter a valid date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Phone number is required')]
    #[Assert\Regex(
        pattern: "/^\d{8}$/", // exactly 8 digits
        message: "Phone number must be exactly 8 digits"
    )]
    private ?int $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email address')]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 500,
        maxMessage: 'Message should not be longer than {{ limit }} characters'
    )]
    private ?string $Message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static // Allow null for date
    {
        $this->date = $date;
        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->Message;
    }

    public function setMessage(string $Message): static
    {
        $this->Message = $Message;
        return $this;
    }
}
