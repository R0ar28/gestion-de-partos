<?php

namespace App\Service;

use App\Entity\EventLog;
use App\Entity\TypeEventLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EventLogService
{
    public function __construct(private EntityManagerInterface $em){}

    public function logEvent(
        ?UserInterface $user,
        int            $typeEventLogId,
        string         $description,
        ?int           $entityId = null,
        int            $indView = 1,
        ?string        $typeEntity = null
    ): void
    {
        $event = new EventLog();
        if ($user) {
            $event->setUser($user);
        }
        $event->setCreatedAt(new \DateTime());
        $event->setTypeEventLog($this->em->getReference(TypeEventLog::class, $typeEventLogId));
        $event->setDescription($description);
        $event->setEntityId($entityId);
        $event->setIndView($indView);
        $event->setTypeEntity($typeEntity);

        $this->em->persist($event);
        $this->em->flush();
    }
}