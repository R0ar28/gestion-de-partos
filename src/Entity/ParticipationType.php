<?php

namespace App\Entity;

use App\Repository\ParticipationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationTypeRepository::class)]
class ParticipationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'participationTypes')]
    private ?MedicalType $medicalType = null;

    /**
     * @var Collection<int, ParticipationDoctor>
     */
    #[ORM\OneToMany(targetEntity: ParticipationDoctor::class, mappedBy: 'participationType')]
    private Collection $participationDoctors;

    public function __construct()
    {
        $this->participationDoctors = new ArrayCollection();
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

    public function getMedicalType(): ?MedicalType
    {
        return $this->medicalType;
    }

    public function setMedicalType(?MedicalType $medicalType): static
    {
        $this->medicalType = $medicalType;

        return $this;
    }

    /**
     * @return Collection<int, ParticipationDoctor>
     */
    public function getParticipationDoctors(): Collection
    {
        return $this->participationDoctors;
    }

    public function addParticipationDoctor(ParticipationDoctor $participationDoctor): static
    {
        if (!$this->participationDoctors->contains($participationDoctor)) {
            $this->participationDoctors->add($participationDoctor);
            $participationDoctor->setParticipationType($this);
        }

        return $this;
    }

    public function removeParticipationDoctor(ParticipationDoctor $participationDoctor): static
    {
        if ($this->participationDoctors->removeElement($participationDoctor)) {
            // set the owning side to null (unless already changed)
            if ($participationDoctor->getParticipationType() === $this) {
                $participationDoctor->setParticipationType(null);
            }
        }

        return $this;
    }
}
