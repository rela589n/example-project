<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Create;

use App\EmployeePortal\Shop\Category\Category;
use App\EmployeePortal\Shop\Product\Product;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class ProductCreatedEvent
{
    private Product $product;

    public function __construct(
        private(set) Uuid $id,
        private(set) string $title,
        private(set) string $description,
        private(set) int $priceUnitAmount,
        private(set) Category $category,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Product
    {
        return $this->product = new Product($this);
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
