<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(length: 180)]
    private ?string $autore= null;

    #[ORM\Column]
    private ?int $quantity = null;
    
    #[ORM\Column]
    private ?int $daily_price = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutore(): ?string
    {
        return $this->autore;
    }

    public function setAutore(string $autore): static
    {
        $this->autore = $autore;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDailyPrice(): ?int
    {
        return $this->daily_price;
    }

    public function setDailyPrice(int $daily_price): static
    {
        $this->daily_price = $daily_price;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
