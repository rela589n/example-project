<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Delete;

use Symfony\Component\Uid\Uuid;

final readonly class EntityDeletedEvent
{
    public function __construct(
        private(set) Uuid $id,
    ) {
    }
}
