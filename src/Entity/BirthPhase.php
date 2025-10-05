<?php

namespace App\Entity;

use App\Repository\BirthPhaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BirthPhaseRepository::class)]
class BirthPhase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'birthPhases')]
    private ?PhaseType $phaseType = null;

    #[ORM\ManyToOne(inversedBy: 'birthPhases')]
    private ?Birth $birth = null;

    #[ORM\ManyToOne(inversedBy: 'birthPhases')]
    private ?User $entityUser = null;

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

    public function getPhaseType(): ?PhaseType
    {
        return $this->phaseType;
    }

    public function setPhaseType(?PhaseType $phaseType): static
    {
        $this->phaseType = $phaseType;

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

    public function getEntityUser(): ?User
    {
        return $this->entityUser;
    }

    public function setEntityUser(?User $entityUser): static
    {
        $this->entityUser = $entityUser;

        return $this;
    }
}
