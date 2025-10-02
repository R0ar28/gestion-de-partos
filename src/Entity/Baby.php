<?php

namespace App\Entity;

use App\Repository\BabyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BabyRepository::class)]
class Baby
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $height = null;

    #[ORM\Column]
    private ?int $wight = null;

    #[ORM\Column]
    private ?bool $activeInd = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'babies')]
    private ?User $entityUser = null;

    #[ORM\ManyToOne(inversedBy: 'babies')]
    private ?Birth $birth = null;

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

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWight(): ?int
    {
        return $this->wight;
    }

    public function setWight(int $wight): static
    {
        $this->wight = $wight;

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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEntityUser(): ?User
    {
        return $this->entityUser;
    }

    public function setEntityUser(?User $entityUser): static
    {
        $this->entityUser = $entityUser;

        return $this;
    }

    public function getBirth(): ?Birth
    {
        return $this->birth;
    }

    public function setBirth(?Birth $birth): static
    {
        $this->birth = $birth;

        return $this;
    }
}
