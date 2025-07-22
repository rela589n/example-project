<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Port;

use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserService
{
    public function __construct(
        public ValidatorInterface $validator,
        #[Autowire('@=service("security.password_hasher_factory").getPasswordHasher("user")')]
        public PasswordHasherInterface $passwordHasher,
        public ClockInterface $clock,
        public UserRepository $userRepository,
        public EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
        public LoggerInterface $logger,
        private NormalizerInterface $serializer,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        // The service itself could contain some ad-hoc infrastructural things like logging, transaction management etc.

        try {
            $this->logger->debug('User registration');

            $command->process($this);

            $this->logger->info('User registration successful');
        } catch (Exception $e) {
            $this->logger->notice('User registration failed', [
                'exception' => $e,
                'data' => $this->serializer->normalize($command, 'array'),
            ]);

            throw $e;
        }
    }
}
