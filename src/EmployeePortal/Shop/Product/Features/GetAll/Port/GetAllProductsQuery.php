<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\GetAll\Port;

use App\EmployeePortal\Shop\Product\Features\GetAll\Filter\TitleFilter;
use App\EmployeePortal\Shop\Product\Product;
use App\EmployeePortal\Shop\Product\ProductCollection;

final class GetAllProductsQuery
{
    /** @var list<array<string, mixed>> */
    private array $products;

    /** @var array|TitleFilter[] */
    private array $filters = [];

    /** @param array<string,string> $filters */
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

        /** @var list<Product> $products */
        $products = $productCollection->match()->toArray();

        $this->products = array_map(static fn (Product $product): array => [
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

    /** @return list<array<string, mixed>> */
    public function getProducts(): array
    {
        return $this->products;
    }
}
