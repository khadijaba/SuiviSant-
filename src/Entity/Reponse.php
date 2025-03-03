<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $body = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datereponse = null;

    #[ORM\Column]
    private ?int $ownerid = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getDatereponse(): ?\DateTimeInterface
    {
        return $this->datereponse;
    }

    public function setDatereponse(\DateTimeInterface $datereponse): static
    {
        $this->datereponse = $datereponse;

        return $this;
    }

    public function getOwnerid(): ?int
    {
        return $this->ownerid;
    }

    public function setOwnerid(int $ownerid): static
    {
        $this->ownerid = $ownerid;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }
}
