<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\GetAll\Port;

use App\EmployeePortal\Shop\Product\ProductCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetAllProductsService
{
    public function __construct(
        public ProductCollection $productCollection,
    ) {
    }

    public function __invoke(GetAllProductsQuery $query): void
    {
        $query->process($this);
    }
}
