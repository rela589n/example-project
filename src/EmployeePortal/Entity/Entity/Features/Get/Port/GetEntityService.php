<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Get\Port;

use App\EmployeePortal\Entity\Entity\EntityCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetEntityService
{
    public function __construct(
        public EntityCollection $entityCollection,
    ) {
    }

    public function __invoke(GetEntityQuery $query): void
    {
        $query->process($this);
    }
}