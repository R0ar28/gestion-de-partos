<?php

namespace App\Entity;

use App\Repository\MedicalTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicalTypeRepository::class)]
class MedicalType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ParticipationType>
     */
    #[ORM\OneToMany(targetEntity: ParticipationType::class, mappedBy: 'medicalType')]
    private Collection $participationTypes;

    /**
     * @var Collection<int, MedicalTeam>
     */
    #[ORM\ManyToMany(targetEntity: MedicalTeam::class, mappedBy: 'medicalType')]
    private Collection $medicalTeams;

    public function __construct()
    {
        $this->participationTypes = new ArrayCollection();
        $this->medicalTeams = new ArrayCollection();
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
     * @return Collection<int, ParticipationType>
     */
    public function getParticipationTypes(): Collection
    {
        return $this->participationTypes;
    }

    public function addParticipationType(ParticipationType $participationType): static
    {
        if (!$this->participationTypes->contains($participationType)) {
            $this->participationTypes->add($participationType);
            $participationType->setMedicalType($this);
        }

        return $this;
    }

    public function removeParticipationType(ParticipationType $participationType): static
    {
        if ($this->participationTypes->removeElement($participationType)) {
            // set the owning side to null (unless already changed)
            if ($participationType->getMedicalType() === $this) {
                $participationType->setMedicalType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedicalTeam>
     */
    public function getMedicalTeams(): Collection
    {
        return $this->medicalTeams;
    }

    public function addMedicalTeam(MedicalTeam $medicalTeam): static
    {
        if (!$this->medicalTeams->contains($medicalTeam)) {
            $this->medicalTeams->add($medicalTeam);
            $medicalTeam->addMedicalType($this);
        }

        return $this;
    }

    public function removeMedicalTeam(MedicalTeam $medicalTeam): static
    {
        if ($this->medicalTeams->removeElement($medicalTeam)) {
            $medicalTeam->removeMedicalType($this);
        }

        return $this;
    }
}
