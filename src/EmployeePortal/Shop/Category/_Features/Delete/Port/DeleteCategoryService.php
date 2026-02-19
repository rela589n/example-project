<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Delete\Port;

use App\EmployeePortal\Shop\Category\CategoryCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteCategoryService
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public CategoryCollection $categoryCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $command->process($this);
    }
}
