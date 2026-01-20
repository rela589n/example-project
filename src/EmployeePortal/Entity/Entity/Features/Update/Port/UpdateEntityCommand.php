<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Update\Port;

use App\EmployeePortal\Entity\Entity\Features\Update\EntityUpdatedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateEntityCommand
{
    public function __construct(
        private(set) Uuid $id,
    ) {
    }

    public function process(UpdateEntityService $service): void
    {
        $entity = $service->entityCollection->get($this->id);

        $event = new EntityUpdatedEvent($entity, $service->now());

        $event->process();

        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
