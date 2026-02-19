<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Update\Port;

use App\EmployeePortal\Entity\Entity\EntityCollection;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateEntityService
{
    public function __construct(
        public ClockInterface $clock,
        public EntityManagerInterface $entityManager,
        public EntityCollection $entityCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateEntityCommand $command): void
    {
        $command->process($this);
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::instance($this->clock->now());
    }
}
