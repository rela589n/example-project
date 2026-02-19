<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Create;

use App\EmployeePortal\Shop\Category\Category;
use App\EmployeePortal\Shop\Product\Description\Description;
use App\EmployeePortal\Shop\Product\Price\Price;
use App\EmployeePortal\Shop\Product\Product;
use App\EmployeePortal\Shop\Product\Title\Title;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class ProductCreatedEvent
{
    private Product $product;

    public function __construct(
        private(set) Uuid $id,
        private(set) Title $title,
        private(set) Description $description,
        private(set) Price $price,
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
