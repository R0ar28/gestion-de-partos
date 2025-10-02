<?php

namespace App\Entity;

use App\Repository\ParticipationDoctorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationDoctorRepository::class)]
class ParticipationDoctor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $observationTxt = null;

    #[ORM\ManyToOne(inversedBy: 'participationDoctors')]
    private ?MedicalTeam $medicalTeam = null;

    #[ORM\ManyToOne(inversedBy: 'participationDoctors')]
    private ?ParticipationType $participationType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObservationTxt(): ?string
    {
        return $this->observationTxt;
    }

    public function setObservationTxt(string $observationTxt): static
    {
        $this->observationTxt = $observationTxt;

        return $this;
    }

    public function getMedicalTeam(): ?MedicalTeam
    {
        return $this->medicalTeam;
    }

    public function setMedicalTeam(?MedicalTeam $medicalTeam): static
    {
        $this->medicalTeam = $medicalTeam;

        return $this;
    }

    public function getParticipationType(): ?ParticipationType
    {
        return $this->participationType;
    }

    public function setParticipationType(?ParticipationType $participationType): static
    {
        $this->participationType = $participationType;

        return $this;
    }
}
