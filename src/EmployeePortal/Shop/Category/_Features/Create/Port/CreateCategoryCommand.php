<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Create\Port;

use App\EmployeePortal\Shop\Category\_Features\Create\CategoryCreatedEvent;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

final readonly class CreateCategoryCommand
{
    public private(set) Uuid $id;

    public function __construct(
        public string $name,
        #[Ignore] // @phpstan-ignore attribute.target
        ?Uuid $id = null,
    ) {
        $this->id = $id ?? Uuid::v7();
    }

    public function process(CreateCategoryService $service): void
    {
        $event = new CategoryCreatedEvent($this->id, $this->name, $service->now());

        $category = $event->process();

        $service->entityManager->persist($category);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
