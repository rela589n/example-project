<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Index\Port;

use Symfony\Component\Uid\Uuid;

final readonly class IndexProductCommand
{
    public function __construct(
        private(set) Uuid $productId,
    ) {
    }

    public function process(IndexProductService $service): void
    {
        $product = $service->productCollection->get($this->productId);

        $service->vespaClient->feedDocument(
            'shop',
            'product',
            $product->id->toRfc4122(),
            [
                'title' => $product->title->title,
                'description' => $product->description->description,
                'category' => $product->category->name,
                'priceUnitAmount' => $product->price->unitAmount,
            ],
        );
    }
}
