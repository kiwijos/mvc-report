<?php

namespace App\Entity\Game;

use App\Repository\Game\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
class Connection
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fromLocationId = null;

    #[ORM\Column]
    private ?int $toLocationId = null;

    #[ORM\Column(length: 10)]
    private ?string $direction = null;

    public function setId(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromLocationId(): ?int
    {
        return $this->fromLocationId;
    }

    public function setFromLocationId(int $fromLocationId): self
    {
        $this->fromLocationId = $fromLocationId;

        return $this;
    }

    public function getToLocationId(): ?int
    {
        return $this->toLocationId;
    }

    public function setToLocationId(int $toLocationId): self
    {
        $this->toLocationId = $toLocationId;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }
}
