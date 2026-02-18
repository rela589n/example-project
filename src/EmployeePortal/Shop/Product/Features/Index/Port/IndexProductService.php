<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Index\Port;

use App\EmployeePortal\Shop\Product\ProductCollection;
use App\Support\Vespa\VespaClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class IndexProductService
{
    public function __construct(
        public ProductCollection $productCollection,
        public VespaClient $vespaClient,
    ) {
    }

    public function __invoke(IndexProductCommand $command): void
    {
        $command->process($this);
    }
}
