<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity;

use App\EmployeePortal\Entity\Entity\_Features\Create\EntityCreatedEvent;
use App\EmployeePortal\Entity\Entity\_Features\Update\EntityUpdatedEvent;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'entities')]
class Entity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $updatedAt;

    public function __construct(EntityCreatedEvent $event)
    {
        $this->id = $event->id;
        $this->createdAt = $event->timestamp;
        $this->updatedAt = $event->timestamp;
    }

    public function update(EntityUpdatedEvent $event): void
    {
        $this->updatedAt = $event->timestamp;
    }
}
