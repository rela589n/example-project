<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Action\Login\Port;

use App\EmployeePortal\Authentication\Domain\User\Email\Email;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Repository\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class LoginUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
        private ClockInterface $clock,
        #[Autowire('@=service("security.password_hasher_factory").getPasswordHasher("'.User::class.'")')]
        private PasswordHasherInterface $passwordHasher,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(LoginUserCommand $command): void
    {
        $logInUser = UserLoggedInEvent::process($this->clock, $this->passwordHasher);

        $event = $logInUser($this->getUser($command), $command->getPassword());

        $this->eventBus->dispatch($event);
    }

    private function getUser(LoginUserCommand $command): User
    {
        return $this->userRepository->findByEmail($this->getEmail($command));
    }

    private function getEmail(LoginUserCommand $command): Email
    {
        return Email::fromString($this->validator, $command->getEmail());
    }
}
