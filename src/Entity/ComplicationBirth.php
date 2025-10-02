<?php

namespace App\Entity;

use App\Repository\ComplicationBirthRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplicationBirthRepository::class)]
class ComplicationBirth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'complicationBirths')]
    private ?ComplicationType $complicationType = null;

    #[ORM\ManyToOne(inversedBy: 'complicationBirths')]
    private ?Birth $birth = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getComplicationType(): ?ComplicationType
    {
        return $this->complicationType;
    }

    public function setComplicationType(?ComplicationType $complicationType): static
    {
        $this->complicationType = $complicationType;

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
