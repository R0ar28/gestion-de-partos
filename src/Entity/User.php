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
     * @var Collection<int, Party>
     */
    #[ORM\OneToMany(targetEntity: Party::class, mappedBy: 'user')]
    private Collection $parties;

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
}
