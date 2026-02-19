<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Search\Port;

use App\Support\Vespa\VespaClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class SearchProductsService
{
    public function __construct(
        public VespaClient $vespaClient,
    ) {
    }

    public function __invoke(SearchProductsQuery $query): void
    {
        $query->process($this);
    }
}
