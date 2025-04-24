<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Stories\Login;

use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class LoginUserService
{
    public function __construct(
        public UserRepository $userRepository,
        public ValidatorInterface $validator,
        public ClockInterface $clock,
        #[Autowire('@=service("security.password_hasher_factory").getPasswordHasher("user")')]
        public PasswordHasherInterface $passwordHasher,
        public EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
        public JWTTokenManagerInterface $tokenManager,
    ) {
    }

    public function __invoke(LoginUserCommand $command): void
    {
        $command->process($this);
    }
}
