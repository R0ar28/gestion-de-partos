<?php

namespace App\Entity;

use App\Repository\PhaseTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhaseTypeRepository::class)]
class PhaseType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, BirthPhase>
     */
    #[ORM\OneToMany(targetEntity: BirthPhase::class, mappedBy: 'phaseType')]
    private Collection $birthPhases;

    public function __construct()
    {
        $this->birthPhases = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, BirthPhase>
     */
    public function getBirthPhases(): Collection
    {
        return $this->birthPhases;
    }

    public function addBirthPhase(BirthPhase $birthPhase): static
    {
        if (!$this->birthPhases->contains($birthPhase)) {
            $this->birthPhases->add($birthPhase);
            $birthPhase->setPhaseType($this);
        }

        return $this;
    }

    public function removeBirthPhase(BirthPhase $birthPhase): static
    {
        if ($this->birthPhases->removeElement($birthPhase)) {
            // set the owning side to null (unless already changed)
            if ($birthPhase->getPhaseType() === $this) {
                $birthPhase->setPhaseType(null);
            }
        }

        return $this;
    }
}
