<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[Vich\Uploadable]
class Expert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $specialite = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $experience = null;

    #[Vich\UploadableField(mapping: "expert_photos", fileNameProperty: "photo")]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png", "image/webp"],
        mimeTypesMessage: "Please upload a valid image file (JPG, PNG, WEBP)."
    )]
    private ?File $photoFile = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, DispoExpert>
     */
    #[ORM\OneToMany(targetEntity: DispoExpert::class, mappedBy: 'Expert', orphanRemoval: true)]
    private Collection $dispoExperts;

    public function __construct()
    {
        $this->dispoExperts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): self
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;

        if ($photoFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, DispoExpert>
     */
    public function getDispoExperts(): Collection
    {
        return $this->dispoExperts;
    }

    public function addDispoExpert(DispoExpert $dispoExpert): static
    {
        if (!$this->dispoExperts->contains($dispoExpert)) {
            $this->dispoExperts->add($dispoExpert);
            $dispoExpert->setExpert($this);
        }

        return $this;
    }

    public function removeDispoExpert(DispoExpert $dispoExpert): static
    {
        if ($this->dispoExperts->removeElement($dispoExpert)) {
            // set the owning side to null (unless already changed)
            if ($dispoExpert->getExpert() === $this) {
                $dispoExpert->setExpert(null);
            }
        }

        return $this;
    }
}
