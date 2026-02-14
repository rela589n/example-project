<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\Features\Update\Port;

use App\EmployeePortal\Shop\Category\Features\Update\CategoryUpdatedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateCategoryCommand
{
    public function __construct(
        private(set) Uuid $id,
        private(set) string $name,
    ) {
    }

    public function process(UpdateCategoryService $service): void
    {
        $category = $service->categoryCollection->get($this->id);

        $event = new CategoryUpdatedEvent($category, $this->name, $service->now());

        $event->process();

        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
