<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Update;

use App\EmployeePortal\Entity\Entity\Entity;
use Carbon\CarbonImmutable;

final readonly class EntityUpdatedEvent
{
    public function __construct(
        private(set) Entity $entity,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): void
    {
        $this->entity->update($this);
    }
}
