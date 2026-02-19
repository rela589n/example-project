<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Update\Port\Api;

final readonly class UpdateCategoryPayload
{
    public function __construct(
        public string $name,
    ) {
    }
}
