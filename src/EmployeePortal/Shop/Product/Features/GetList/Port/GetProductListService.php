<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\GetList\Port;

use App\EmployeePortal\Shop\Product\ProductCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetProductListService
{
    public function __construct(
        public ProductCollection $productCollection,
    ) {
    }

    public function __invoke(GetProductListQuery $query): void
    {
        $query->process($this);
    }
}
