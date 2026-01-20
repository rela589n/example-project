<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Create\Port;

use App\EmployeePortal\Entity\Entity\Features\Create\EntityCreatedEvent;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

final readonly class CreateEntityCommand
{
    public private(set) Uuid $id;

    public function __construct(
        #[Ignore]
        ?Uuid $id = null,
    ) {
        $this->id = $id ?? Uuid::v7();
    }

    public function process(CreateEntityService $service): void
    {
        $event = new EntityCreatedEvent($this->id, $service->now());

        $entity = $event->process();

        $service->entityManager->persist($entity);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
