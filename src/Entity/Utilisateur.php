<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Consultation;

#[ORM\Entity]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer un email.')]
    #[Assert\Email(message: 'Veuillez entrer un email valide.')]
    private string $email;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[Assert\NotBlank(message: 'Veuillez entrer un mot de passe.')]
    #[Assert\Length(min: 6, minMessage: 'Votre mot de passe doit contenir au moins 6 caractères.')]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre nom.')]
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre prénom.')]
    private string $prenom;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez entrer votre date de naissance.')]
    private \DateTimeInterface $date_naissance;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre sexe.')]
    private string $sexe;

    #[Assert\NotBlank(message: 'Vous devez accepter les conditions.')]
    private ?bool $agreeTerms = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Consultation::class, orphanRemoval: true)]
    private Collection $consultations;

    // 🔹 Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): \DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function getAgreeTerms(): ?bool
    {
        return $this->agreeTerms;
    }

    public function setAgreeTerms(?bool $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;
        return $this;
    }

    // 🔹 Méthodes de l'interface UserInterface

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // 🔹 Relations avec Consultation

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations[] = $consultation;
            $consultation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            if ($consultation->getUtilisateur() === $this) {
                $consultation->setUtilisateur(null);
            }
        }

        return $this;
    }
}
