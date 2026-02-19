<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\Create\Port;

use App\EmployeePortal\Blog\User\UserCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreatePostService
{
    public function __construct(
        public ValidatorInterface $validator,
        public EntityManagerInterface $entityManager,
        public UserCollection $userCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreatePostCommand $command): void
    {
        $command->process($this);
    }
}
