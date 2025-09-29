<?php

namespace App\Entity;

use App\Repository\PartyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartyRepository::class)]
class Party
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    private ?PartyType $partyType = null;

    #[ORM\Column(length: 255)]
    private ?string $identification = null;

    #[ORM\ManyToOne]
    private ?IdentificatorType $identificationType = null;

    #[ORM\Column]
    private ?bool $activeInd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $entityUserId = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPartyType(): ?PartyType
    {
        return $this->partyType;
    }

    public function setPartyType(?PartyType $partyType): static
    {
        $this->partyType = $partyType;

        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(string $identification): static
    {
        $this->identification = $identification;

        return $this;
    }

    public function getIdentificationType(): ?IdentificatorType
    {
        return $this->identificationType;
    }

    public function setIdentificationType(?IdentificatorType $identificationType): static
    {
        $this->identificationType = $identificationType;

        return $this;
    }

    public function isActiveInd(): ?bool
    {
        return $this->activeInd;
    }

    public function setActiveInd(bool $activeInd): static
    {
        $this->activeInd = $activeInd;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEntityUserId(): ?int
    {
        return $this->entityUserId;
    }

    public function setEntityUserId(int $entityUserId): static
    {
        $this->entityUserId = $entityUserId;

        return $this;
    }
}
