<?php

namespace App\Entity;

use App\Repository\TypeEventLogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeEventLogRepository::class)]
class TypeEventLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cssColor = null;

    /**
     * @var Collection<int, EventLog>
     */
    #[ORM\OneToMany(targetEntity: EventLog::class, mappedBy: 'typeEventLog')]
    private Collection $eventLogs;

    public function __construct()
    {
        $this->eventLogs = new ArrayCollection();
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

    public function getCssColor(): ?string
    {
        return $this->cssColor;
    }

    public function setCssColor(string $cssColor): static
    {
        $this->cssColor = $cssColor;

        return $this;
    }

    /**
     * @return Collection<int, EventLog>
     */
    public function getEventLogs(): Collection
    {
        return $this->eventLogs;
    }

    public function addEventLog(EventLog $eventLog): static
    {
        if (!$this->eventLogs->contains($eventLog)) {
            $this->eventLogs->add($eventLog);
            $eventLog->setTypeEventLog($this);
        }

        return $this;
    }

    public function removeEventLog(EventLog $eventLog): static
    {
        if ($this->eventLogs->removeElement($eventLog)) {
            // set the owning side to null (unless already changed)
            if ($eventLog->getTypeEventLog() === $this) {
                $eventLog->setTypeEventLog(null);
            }
        }

        return $this;
    }
}
