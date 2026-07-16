<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Create\Port;

use App\EmployeePortal\Shop\Product\_Features\Create\ProductCreatedEvent;
use App\EmployeePortal\Shop\Product\Product;
use PhPhD\ExceptionalMatcher\Rule\Object\Property\Catch_;
use PhPhD\ExceptionalMatcher\Rule\Object\Try_;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Exception\ValidationFailedException;

use const PhPhD\ExceptionalMatcher\Integration\Validator\Formatter\Embedded\embedded_violations;

#[Try_]
final readonly class CreateProductCommand
{
    private(set) Uuid $id;

    public function __construct(
        #[Catch_(ValidationFailedException::class, from: [Product::class, '$title::set'], format: embedded_violations)]
        private string $title,
        #[Catch_(ValidationFailedException::class, from: [Product::class, '$description::set'], format: embedded_violations)]
        private string $description,
        #[Catch_(ValidationFailedException::class, from: [Product::class, '$priceUnitAmount::set'], format: embedded_violations)]
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
