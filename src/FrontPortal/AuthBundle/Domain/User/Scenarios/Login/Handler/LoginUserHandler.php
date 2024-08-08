<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\LoginUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class LoginUserHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(LoginUserCommand $command): void
    {
        $event = new UserLoggedInEvent(
            $this->findUser($this->getEmail($command)),
            $this->getPassword($command),
        );

$event->process();
    }

    private function findUser(Closure $emailFn): Closure
    {
        return function () use ($emailFn) {
            $email = $emailFn();

            return $this->entityManager->getRepository(User::class)
                ->findOneBy(['email.email' => $email]) ?? throw new UserNotFoundException($email);
        };
    }

    private function getEmail(LoginUserCommand $command): Closure
    {
        return fn () => Email::fromUserInput($command->getEmail(), $this->validator);
    }

    private function getPassword(LoginUserCommand $command): Closure
    {
        return static fn () => $command->getPassword();
    }
}
