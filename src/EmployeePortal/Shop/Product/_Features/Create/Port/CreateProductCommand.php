<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Create\Port;

use App\EmployeePortal\Shop\Product\_Features\Create\ProductCreatedEvent;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

final readonly class CreateProductCommand
{
    private(set) Uuid $id;

    public function __construct(
        private string $title,
        private string $description,
        private int $priceUnitAmount,
        private Uuid $categoryId,
        #[Ignore] // @phpstan-ignore attribute.target
        ?Uuid $id = null,
    ) {
        $this->id = $id ?? Uuid::v7();
    }

    public function process(CreateProductService $service): void
    {
        $event = new ProductCreatedEvent(
            $this->id,
            $this->title,
            $this->description,
            $this->priceUnitAmount,
            $service->categoryCollection->get($this->categoryId),
            $service->now(),
        );

        $entity = $event->process();

        $service->entityManager->persist($entity);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
