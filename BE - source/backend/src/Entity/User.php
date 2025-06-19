<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $userRoles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserRace::class, cascade: ['persist', 'remove'])]
    private Collection $userRaces;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    #[ORM\Column(length: 180)]
    private ?string $surname = null;


    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->userRaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {

        return $this->userRoles->map(fn(Role $r) => $r->getName())->toArray();
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // public function getRefreshToken(): ?string
    // {
    //     return $this->refreshToken;
    // }

    // public function setRefreshToken(?string $refreshToken): static
    // {
    //     $this->refreshToken = $refreshToken;

    //     return $this;
    // }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            $userRole->removeUser($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, UserRace>
     */
    public function getUserRaces(): Collection
    {
        return $this->userRaces;
    }

    public function addUserRace(UserRace $userRace): static
    {
        if (!$this->userRaces->contains($userRace)) {
            $this->userRaces->add($userRace);
            $userRace->setUser($this);
        }

        return $this;
    }

    public function removeUserRace(UserRace $userRace): static
    {
        if ($this->userRaces->removeElement($userRace)) {
            if ($userRace->getUser() === $this) {
                $userRace->setUser(null);
            }
        }

        return $this;
    }
}
