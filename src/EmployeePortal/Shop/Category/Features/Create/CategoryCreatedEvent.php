<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\Features\Create;

use App\EmployeePortal\Shop\Category\Category;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class CategoryCreatedEvent
{
    private Category $category;

    public function __construct(
        private(set) Uuid $id,
        private(set) string $name,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Category
    {
        return $this->category = new Category($this);
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
