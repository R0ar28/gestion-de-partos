<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
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
    #[ORM\OneToMany(targetEntity: Birth::class, mappedBy: 'room')]
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
            $birth->setRoom($this);
        }

        return $this;
    }

    public function removeBirth(Birth $birth): static
    {
        if ($this->births->removeElement($birth)) {
            // set the owning side to null (unless already changed)
            if ($birth->getRoom() === $this) {
                $birth->setRoom(null);
            }
        }

        return $this;
    }
}
