<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Delete\Port;

use App\EmployeePortal\Entity\Entity\EntityCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteEntityService
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public EntityCollection $entityCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteEntityCommand $command): void
    {
        $command->process($this);
    }
}
