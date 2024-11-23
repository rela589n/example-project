<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Action\Register\Port;

use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserServiceContext
{
    public function __construct(
        public ValidatorInterface $validator,
        #[Autowire('@=service("security.password_hasher_factory").getPasswordHasher("'.User::class.'")')]
        public PasswordHasherInterface $passwordHasher,
        public ClockInterface $clock,
        public UserRepository $userRepository,
        public EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        // The service itself could contain some ad-hoc technical concerns like logging, transaction management etc.

        try {
            $this->logger->debug('User registration');

            $command->run($this);

            $this->logger->info('User registration successful');
        } catch (Exception $e) {
            $this->logger->notice('User registration failed', ['exception' => $e, 'data' => $this->serializer->serialize($command, 'array')]);

            throw $e;
        }
    }
}
