<?php

namespace App\Entity;

use App\Repository\BirthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BirthRepository::class)]
class Birth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $gestacionalAgeWeeks = null;

    #[ORM\ManyToOne(inversedBy: 'births')]
    private ?Mother $mother = null;

    #[ORM\ManyToOne(inversedBy: 'births')]
    private ?BirthType $birthType = null;

    #[ORM\ManyToOne(inversedBy: 'births')]
    private ?Room $room = null;

    /**
     * @var Collection<int, ComplicationBirth>
     */
    #[ORM\OneToMany(targetEntity: ComplicationBirth::class, mappedBy: 'birth')]
    private Collection $complicationBirths;

    /**
     * @var Collection<int, Baby>
     */
    #[ORM\OneToMany(targetEntity: Baby::class, mappedBy: 'birth')]
    private Collection $babies;

    #[ORM\Column]
    private ?bool $activeInd = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'births')]
    private ?User $entityUser = null;

    /**
     * @var Collection<int, BirthPhase>
     */
    #[ORM\OneToMany(targetEntity: BirthPhase::class, mappedBy: 'birth')]
    private Collection $birthPhases;

    public function __construct()
    {
        $this->complicationBirths = new ArrayCollection();
        $this->babies = new ArrayCollection();
        $this->birthPhases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGestacionalAgeWeeks(): ?int
    {
        return $this->gestacionalAgeWeeks;
    }

    public function setGestacionalAgeWeeks(int $gestacionalAgeWeeks): static
    {
        $this->gestacionalAgeWeeks = $gestacionalAgeWeeks;

        return $this;
    }

    public function getMother(): ?Mother
    {
        return $this->mother;
    }

    public function setMother(?Mother $mother): static
    {
        $this->mother = $mother;

        return $this;
    }

    public function getBirthType(): ?BirthType
    {
        return $this->birthType;
    }

    public function setBirthType(?BirthType $birthType): static
    {
        $this->birthType = $birthType;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Collection<int, ComplicationBirth>
     */
    public function getComplicationBirths(): Collection
    {
        return $this->complicationBirths;
    }

    public function addComplicationBirth(ComplicationBirth $complicationBirth): static
    {
        if (!$this->complicationBirths->contains($complicationBirth)) {
            $this->complicationBirths->add($complicationBirth);
            $complicationBirth->setBirth($this);
        }

        return $this;
    }

    public function removeComplicationBirth(ComplicationBirth $complicationBirth): static
    {
        if ($this->complicationBirths->removeElement($complicationBirth)) {
            // set the owning side to null (unless already changed)
            if ($complicationBirth->getBirth() === $this) {
                $complicationBirth->setBirth(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Baby>
     */
    public function getBabies(): Collection
    {
        return $this->babies;
    }

    public function addBaby(Baby $baby): static
    {
        if (!$this->babies->contains($baby)) {
            $this->babies->add($baby);
            $baby->setBirth($this);
        }

        return $this;
    }

    public function removeBaby(Baby $baby): static
    {
        if ($this->babies->removeElement($baby)) {
            // set the owning side to null (unless already changed)
            if ($baby->getBirth() === $this) {
                $baby->setBirth(null);
            }
        }

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
            $birthPhase->setBirth($this);
        }

        return $this;
    }

    public function removeBirthPhase(BirthPhase $birthPhase): static
    {
        if ($this->birthPhases->removeElement($birthPhase)) {
            // set the owning side to null (unless already changed)
            if ($birthPhase->getBirth() === $this) {
                $birthPhase->setBirth(null);
            }
        }

        return $this;
    }

}
