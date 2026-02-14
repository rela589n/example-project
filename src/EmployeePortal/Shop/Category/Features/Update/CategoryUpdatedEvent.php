<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\Features\Update;

use App\EmployeePortal\Shop\Category\Category;
use Carbon\CarbonImmutable;

final readonly class CategoryUpdatedEvent
{
    public function __construct(
        private(set) Category $category,
        private(set) string $name,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): void
    {
        $this->category->update($this);
    }
}
