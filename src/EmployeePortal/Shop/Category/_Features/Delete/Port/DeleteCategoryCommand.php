<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Delete\Port;

use App\EmployeePortal\Shop\Category\_Features\Delete\CategoryDeletedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class DeleteCategoryCommand
{
    public function __construct(
        private(set) Uuid $id,
    ) {
    }

    public function process(DeleteCategoryService $service): void
    {
        $category = $service->categoryCollection->get($this->id);

        $event = new CategoryDeletedEvent($this->id);

        $service->entityManager->remove($category);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
