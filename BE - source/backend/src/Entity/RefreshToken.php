<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_id', fields: ['id'])]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $device = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $refreshToken = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private array $roles = [];


    // funzioni

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): static
    {
        $this->device = $device;

        return $this;
    }



    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    public function getRTIdentifier(): string
    {
        return (string) $this->id;
    }
    

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
