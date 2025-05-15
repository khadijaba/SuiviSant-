<?php

namespace App\Entity;

use App\Repository\ProgrammeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeRepository::class)]
class Programme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $progression = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $evaluation = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $likes = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $dislikes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

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
} 