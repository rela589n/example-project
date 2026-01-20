<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Create;

use App\EmployeePortal\Entity\Entity\Entity;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class EntityCreatedEvent
{
    private Entity $entity;

    public function __construct(
        private(set) Uuid $id,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Entity
    {
        return $this->entity = new Entity($this);
    }
}
