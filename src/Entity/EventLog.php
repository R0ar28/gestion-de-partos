<?php

namespace App\Entity;

use App\Repository\EventLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventLogRepository::class)]
class EventLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $typeEntity = null;

    #[ORM\Column]
    private ?int $entityId = null;

    #[ORM\Column]
    private ?bool $indView = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;
    #[ORM\ManyToOne(inversedBy: 'eventLogs')]
    private ?TypeEventLog $typeEventLog = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true)]
    private ?User $user = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeEntity(): ?string
    {
        return $this->typeEntity;
    }

    public function setTypeEntity(string $typeEntity): static
    {
        $this->typeEntity = $typeEntity;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function isIndView(): ?bool
    {
        return $this->indView;
    }

    public function setIndView(bool $indView): static
    {
        $this->indView = $indView;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTypeEventLog(): ?TypeEventLog
    {
        return $this->typeEventLog;
    }

    public function setTypeEventLog(?TypeEventLog $typeEventLog): static
    {
        $this->typeEventLog = $typeEventLog;

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
}
