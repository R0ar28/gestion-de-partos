<?php

namespace App\Entity;

use App\Repository\MedicalTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicalTeamRepository::class)]
class MedicalTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $rut = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column(length: 255)]
    private ?string $birthDate = null;

    #[ORM\Column]
    private ?bool $activeInd = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, MedicalType>
     */
    #[ORM\ManyToMany(targetEntity: MedicalType::class, inversedBy: 'medicalTeams')]
    private Collection $medicalType;

    /**
     * @var Collection<int, ParticipationDoctor>
     */
    #[ORM\OneToMany(targetEntity: ParticipationDoctor::class, mappedBy: 'medicalTeam')]
    private Collection $participationDoctors;

    #[ORM\ManyToOne(inversedBy: 'medicalTeams')]
    private ?User $user = null;

    public function __construct()
    {
        $this->medicalType = new ArrayCollection();
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

    public function getRut(): ?string
    {
        return $this->rut;
    }

    public function setRut(string $rut): static
    {
        $this->rut = $rut;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    public function setBirthDate(string $birthDate): static
    {
        $this->birthDate = $birthDate;

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

    /**
     * @return Collection<int, MedicalType>
     */
    public function getMedicalType(): Collection
    {
        return $this->medicalType;
    }

    public function addMedicalType(MedicalType $medicalType): static
    {
        if (!$this->medicalType->contains($medicalType)) {
            $this->medicalType->add($medicalType);
        }

        return $this;
    }

    public function removeMedicalType(MedicalType $medicalType): static
    {
        $this->medicalType->removeElement($medicalType);

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
            $participationDoctor->setMedicalTeam($this);
        }

        return $this;
    }

    public function removeParticipationDoctor(ParticipationDoctor $participationDoctor): static
    {
        if ($this->participationDoctors->removeElement($participationDoctor)) {
            // set the owning side to null (unless already changed)
            if ($participationDoctor->getMedicalTeam() === $this) {
                $participationDoctor->setMedicalTeam(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
