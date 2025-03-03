<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Utilisateurr implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id ;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer un email.')]
    #[Assert\Email(message: 'Veuillez entrer un email valide.')]
    private string $email;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;


    #[ORM\Column(type: 'string', length: 255)]
   
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    
    private string $prenom;

    #[ORM\Column(type: 'date')]
   
    private \DateTimeInterface $date_naissance;

    #[ORM\Column(type: 'string', length: 10)]
   
    private string $sexe;
      
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $resetToken = null;

    private ?string $motDePassConfirmation;


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

    public function getMotDePassConfirmation(): ?string
    {
        return $this->motDePassConfirmation;
    }

    public function setMotDePassConfirmation(string $motDePassConfirmation): self
    {
        $this->motDePassConfirmation = $motDePassConfirmation;

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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    // 🔹 Méthodes de l'interface UserInterface

    public function eraseCredentials()
    {
       
    }

 

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
