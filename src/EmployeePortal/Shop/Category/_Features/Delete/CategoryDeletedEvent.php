<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Delete;

use Symfony\Component\Uid\Uuid;

final readonly class CategoryDeletedEvent
{
    public function __construct(
        private(set) Uuid $id,
    ) {
    }
}
