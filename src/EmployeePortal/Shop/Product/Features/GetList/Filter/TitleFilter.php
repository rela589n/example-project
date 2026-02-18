<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\GetList\Filter;

use App\EmployeePortal\Shop\Product\ProductCollection;

final readonly class TitleFilter
{
    public function __construct(
        private string $title,
    ) {
    }

    public function apply(ProductCollection $collection): ProductCollection
    {
        return $collection->whereTitle($this->title);
    }
}
