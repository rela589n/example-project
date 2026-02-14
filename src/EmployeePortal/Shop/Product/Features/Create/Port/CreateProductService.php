<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Create\Port;

use App\EmployeePortal\Shop\Category\CategoryCollection;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateProductService
{
    public function __construct(
        public ClockInterface $clock,
        public EntityManagerInterface $entityManager,
        public CategoryCollection $categoryCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $command->process($this);
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::instance($this->clock->now());
    }
}
