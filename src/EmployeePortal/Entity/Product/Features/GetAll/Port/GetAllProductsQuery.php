<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Product\Features\GetAll\Port;

use App\EmployeePortal\Entity\Product\Features\GetAll\Filter\TitleFilter;
use App\EmployeePortal\Entity\Product\Product;
use App\EmployeePortal\Entity\Product\ProductCollection;

final class GetAllProductsQuery
{
    /** @var array<string,mixed> */
    private array $products;

    /** @var array|TitleFilter[] */
    private array $filters = [];

    public function __construct(
        array $filters,
    ) {
        if (isset($filters['title'])) {
            $this->filters [] = new TitleFilter($filters['title']);
        }
    }

    public function process(GetAllProductsService $service): void
    {
        $productCollection = array_reduce(
            $this->filters,
            static fn (ProductCollection $collection, TitleFilter $filter): ProductCollection => $filter->apply($collection),
            $service->productCollection,
        );

        $products = $productCollection->match()->toArray();

        $this->products = array_map(static fn (Product $product) => [
            'id' => $product->id->toRfc4122(),
            'title' => $product->title->title,
            'priceUnitAmount' => $product->price->unitAmount,
            'category' => [
                'name' => $product->category->name,
            ],
            'createdAt' => $product->createdAt->toAtomString(),
            'updatedAt' => $product->updatedAt->toAtomString(),
        ], $products);
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
