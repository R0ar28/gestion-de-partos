<?php

namespace App\Entity;

use App\Repository\BirthTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BirthTypeRepository::class)]
class BirthType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Birth>
     */
    #[ORM\OneToMany(targetEntity: Birth::class, mappedBy: 'birthType')]
    private Collection $births;

    public function __construct()
    {
        $this->births = new ArrayCollection();
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
     * @return Collection<int, Birth>
     */
    public function getBirths(): Collection
    {
        return $this->births;
    }

    public function addBirth(Birth $birth): static
    {
        if (!$this->births->contains($birth)) {
            $this->births->add($birth);
            $birth->setBirthType($this);
        }

        return $this;
    }

    public function removeBirth(Birth $birth): static
    {
        if ($this->births->removeElement($birth)) {
            // set the owning side to null (unless already changed)
            if ($birth->getBirthType() === $this) {
                $birth->setBirthType(null);
            }
        }

        return $this;
    }
}
