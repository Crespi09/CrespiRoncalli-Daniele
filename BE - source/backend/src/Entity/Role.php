<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $name = '';

    #[ORM\ManyToMany(User::class, mappedBy: 'userRoles')]
    private Collection $users;
    
    #[ORM\Column(type: 'boolean')]
    private bool $can_create = false;

    #[ORM\Column(type: 'boolean')]
    private bool $can_read = false;
    
    #[ORM\Column(type: 'boolean')]
    private bool $can_update = false;
    
    #[ORM\Column(type: 'boolean')]
    private bool $can_delete = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

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

    public function getCan_create(): ?bool
    {
        return $this->can_create;
    }

    public function setCan_create(bool $can_create): static
    {
        $this->can_create = $can_create;

        return $this;
    }

    
    public function getCan_read(): ?bool
    {
        return $this->can_read;
    }

    public function setCan_read(bool $can_read): static
    {
        $this->can_read = $can_read;

        return $this;
    }


    public function getCan_update(): ?bool
    {
        return $this->can_create;
    }

    public function setcan_update(bool $can_update): static
    {
        $this->can_update = $can_update;

        return $this;
    }


    public function getCan_delete(): ?bool
    {
        return $this->can_delete;
    }

    public function setCan_delete(bool $can_delete): static
    {
        $this->can_delete = $can_delete;

        return $this;
    }

    public function isCanCreate(): ?bool
    {
        return $this->can_create;
    }

    public function setCanCreate(bool $can_create): static
    {
        $this->can_create = $can_create;

        return $this;
    }

    public function isCanRead(): ?bool
    {
        return $this->can_read;
    }

    public function setCanRead(bool $can_read): static
    {
        $this->can_read = $can_read;

        return $this;
    }

    public function isCanUpdate(): ?bool
    {
        return $this->can_update;
    }

    public function setCanUpdate(bool $can_update): static
    {
        $this->can_update = $can_update;

        return $this;
    }

    public function isCanDelete(): ?bool
    {
        return $this->can_delete;
    }

    public function setCanDelete(bool $can_delete): static
    {
        $this->can_delete = $can_delete;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }


}
