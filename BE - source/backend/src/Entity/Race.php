<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\Double;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $place = null;

    #[ORM\Column]
    private ?DateTime $date = null;

    #[ORM\Column]
    private ?float $COEFF = null;

    #[ORM\Column]
    private ?int $max_partecipanti = null;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: UserRace::class, cascade: ['persist', 'remove'])]
    private Collection $userRaces;


    public function __construct()
    {
        $this->userRaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCOEFF(): ?float
    {
        return $this->COEFF;
    }

    public function setCOEFF(float $COEFF): static
    {
        $this->COEFF = $COEFF;

        return $this;
    }

    public function getMaxPartecipanti(): ?int
    {
        return $this->max_partecipanti;
    }

    public function setMaxPartecipanti(int $max_partecipanti): static
    {
        $this->max_partecipanti = $max_partecipanti;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function addUserRace(UserRace $userRace): static
    {
        if (!$this->userRaces->contains($userRace)) {
            $this->userRaces->add($userRace);
            $userRace->setRace($this);
        }

        return $this;
    }

    public function removeUserRace(UserRace $userRace): static
    {
        if ($this->userRaces->removeElement($userRace)) {
            if ($userRace->getRace() === $this) {
                $userRace->setRace(null);
            }
        }

        return $this;
    }
}
