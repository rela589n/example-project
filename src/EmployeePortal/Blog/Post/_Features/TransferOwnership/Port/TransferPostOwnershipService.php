<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\TransferOwnership\Port;

use App\EmployeePortal\Blog\Post\PostCollection;
use App\EmployeePortal\Blog\User\UserCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class TransferPostOwnershipService
{
    public function __construct(
        public ValidatorInterface $validator,
        public EntityManagerInterface $entityManager,
        public UserCollection $userCollection,
        public PostCollection $postCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(TransferPostOwnershipCommand $command): void
    {
        $command->process($this);
    }
}
