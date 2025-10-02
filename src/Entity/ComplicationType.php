<?php

namespace App\Entity;

use App\Repository\ComplicationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplicationTypeRepository::class)]
class ComplicationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ComplicationBirth>
     */
    #[ORM\OneToMany(targetEntity: ComplicationBirth::class, mappedBy: 'complicationType')]
    private Collection $complicationBirths;

    public function __construct()
    {
        $this->complicationBirths = new ArrayCollection();
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
            $complicationBirth->setComplicationType($this);
        }

        return $this;
    }

    public function removeComplicationBirth(ComplicationBirth $complicationBirth): static
    {
        if ($this->complicationBirths->removeElement($complicationBirth)) {
            // set the owning side to null (unless already changed)
            if ($complicationBirth->getComplicationType() === $this) {
                $complicationBirth->setComplicationType(null);
            }
        }

        return $this;
    }
}
