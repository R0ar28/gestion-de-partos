<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['All', 'GlobalStats'])]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?bool $activeInd = null;

    #[ORM\Column]
    private ?int $entityUserId = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(
        name: 'user_role',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    )]
    private Collection $roles;

    #[ORM\Column(length: 255)]
    private ?string $namePerson = null;

    /**
     * @var Collection<int, Baby>
     */
    #[ORM\OneToMany(targetEntity: Baby::class, mappedBy: 'entityUser')]
    private Collection $babies;

    /**
     * @var Collection<int, MedicalTeam>
     */
    #[ORM\OneToMany(targetEntity: MedicalTeam::class, mappedBy: 'user')]
    private Collection $medicalTeams;

    /**
     * @var Collection<int, Birth>
     */
    #[ORM\OneToMany(targetEntity: Birth::class, mappedBy: 'entityUser')]
    private Collection $births;

    /**
     * @var Collection<int, BirthPhase>
     */
    #[ORM\OneToMany(targetEntity: BirthPhase::class, mappedBy: 'entityUser')]
    private Collection $birthPhases;

    /**
     * @var Collection<int, Mother>
     */
    #[ORM\OneToMany(targetEntity: Mother::class, mappedBy: 'entityUser')]
    private Collection $mothers;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->parties = new ArrayCollection();
        $this->babies = new ArrayCollection();
        $this->medicalTeams = new ArrayCollection();
        $this->births = new ArrayCollection();
        $this->birthPhases = new ArrayCollection();
        $this->mothers = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getEntityUserId(): ?int
    {
        return $this->entityUserId;
    }

    public function setEntityUserId(int $entityUserId): static
    {
        $this->entityUserId = $entityUserId;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles->map(fn(Role $roles) => $roles->getName())->toArray();
        return array_unique($roles);
    }

    public function getRolesTxtName(): array
    {
        return $this->roles->map(fn(Role $roles) => $roles->getTextName())->toArray();
    }

    public function getRolesId(): array
    {
        return $this->roles->map(fn(Role $roles) => $roles->getId())->toArray();
    }

    public function addRole(Role $roles): self
    {
        if (!$this->roles->contains($roles)) {
            $this->roles->add($roles);
        }
        return $this;
    }

    public function removeRole(Role $roles): self
    {
        $this->roles->removeElement($roles);
        return $this;
    }

    public function getRolesData(): array
    {
        return $this->roles->map(fn(Role $role) => [
            'id' => $role->getId(),
            'name' => $role->getName(),
            'textName' => $role->getTextName(),
        ])->toArray();
    }

    public function getUserIdentifier(): string
    {
        return $this->id ?? '';
    }

    /** @deprecated Symfony < 5.3 */
    public function getUsername(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Si tuvieras un plainPassword temporal, lo borras aquí
    }

    public function getNamePerson(): ?string
    {
        return $this->namePerson;
    }

    public function setNamePerson(string $namePerson): static
    {
        $this->namePerson = $namePerson;

        return $this;
    }

    /**
     * @return Collection<int, Party>
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Party $party): static
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
            $party->setUser($this);
        }

        return $this;
    }

    public function removeParty(Party $party): static
    {
        if ($this->parties->removeElement($party)) {
            // set the owning side to null (unless already changed)
            if ($party->getUser() === $this) {
                $party->setUser(null);
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
            $baby->setEntityUser($this);
        }

        return $this;
    }

    public function removeBaby(Baby $baby): static
    {
        if ($this->babies->removeElement($baby)) {
            // set the owning side to null (unless already changed)
            if ($baby->getEntityUser() === $this) {
                $baby->setEntityUser(null);
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
            $medicalTeam->setUser($this);
        }

        return $this;
    }

    public function removeMedicalTeam(MedicalTeam $medicalTeam): static
    {
        if ($this->medicalTeams->removeElement($medicalTeam)) {
            // set the owning side to null (unless already changed)
            if ($medicalTeam->getUser() === $this) {
                $medicalTeam->setUser(null);
            }
        }

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
            $birth->setEntityUser($this);
        }

        return $this;
    }

    public function removeBirth(Birth $birth): static
    {
        if ($this->births->removeElement($birth)) {
            // set the owning side to null (unless already changed)
            if ($birth->getEntityUser() === $this) {
                $birth->setEntityUser(null);
            }
        }

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
            $birthPhase->setEntityUser($this);
        }

        return $this;
    }

    public function removeBirthPhase(BirthPhase $birthPhase): static
    {
        if ($this->birthPhases->removeElement($birthPhase)) {
            // set the owning side to null (unless already changed)
            if ($birthPhase->getEntityUser() === $this) {
                $birthPhase->setEntityUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mother>
     */
    public function getMothers(): Collection
    {
        return $this->mothers;
    }

    public function addMother(Mother $mother): static
    {
        if (!$this->mothers->contains($mother)) {
            $this->mothers->add($mother);
            $mother->setEntityUser($this);
        }

        return $this;
    }

    public function removeMother(Mother $mother): static
    {
        if ($this->mothers->removeElement($mother)) {
            // set the owning side to null (unless already changed)
            if ($mother->getEntityUser() === $this) {
                $mother->setEntityUser(null);
            }
        }

        return $this;
    }

}
