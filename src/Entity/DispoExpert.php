<?php

namespace App\Entity;

use App\Repository\DispoExpertRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: DispoExpertRepository::class)]
class DispoExpert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "The date cannot be blank.")]
    #[Assert\Type("\DateTimeInterface", message: "The date is not valid.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "The time cannot be blank.")]
    #[Assert\Type("\DateTimeInterface", message: "The time is not valid.")]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: "The location cannot be blank.")]
    #[Assert\Length(
        min: 3, 
        minMessage: "The location must be at least {{ limit }} characters long."
    )]
    private ?string $location = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "The status cannot be null.")]
    private ?bool $status = null;

    #[ORM\OneToOne(mappedBy: 'dispoExpert', targetEntity: RendezVous::class, cascade: ['persist', 'remove'])]
    private ?RendezVous $rendezVous = null;

    #[ORM\ManyToOne(inversedBy: 'dispoExperts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "An expert must be assigned.")]
    private ?Expert $expert = null;

    // ✅ Custom Validation: Prevent Past Dates & Times
    #[Assert\Callback]
    public function validateDateAndTime(ExecutionContextInterface $context): void
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0); // Normalize today's date for comparison

        if ($this->date && $this->date < $today) {
            $context->buildViolation("The date cannot be in the past.")
                ->atPath('date')
                ->addViolation();
        }

        if ($this->date && $this->date == $today) {
            $now = new \DateTime();
            if ($this->time && $this->time < $now) {
                $context->buildViolation("The time cannot be in the past.")
                    ->atPath('time')
                    ->addViolation();
            }
        }
    }

    // ✅ Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getRendezVous(): ?RendezVous
    {
        return $this->rendezVous;
    }

    public function setRendezVous(?RendezVous $rendezVous): static
    {
        $this->rendezVous = $rendezVous;

        if ($rendezVous !== null && $rendezVous->getDispoExpert() !== $this) {
            $rendezVous->setDispoExpert($this);
        }

        return $this;
    }

    public function getExpert(): ?Expert
    {
        return $this->expert;
    }

    public function setExpert(?Expert $expert): static
    {
        $this->expert = $expert;
        return $this;
    }
}
