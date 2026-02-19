<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Delete\Port;

use App\EmployeePortal\Entity\Entity\_Features\Delete\EntityDeletedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class DeleteEntityCommand
{
    public function __construct(
        private(set) Uuid $id,
    ) {
    }

    public function process(DeleteEntityService $service): void
    {
        $entity = $service->entityCollection->get($this->id);

        $event = new EntityDeletedEvent($this->id);

        $service->entityManager->remove($entity);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
