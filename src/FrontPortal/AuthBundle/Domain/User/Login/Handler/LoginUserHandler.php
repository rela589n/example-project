<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Login\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Login\LoginUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class LoginUserHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(LoginUserCommand $command): void
    {
        $event = $this->createEvent($command);

        $this->eventBus->dispatch($event());

        $this->entityManager->persist($event);
    }

    private function createEvent(LoginUserCommand $command): UserLoggedInEvent
    {
        $email = $this->getEmail($command);
        $user = $this->findUser($email);

        return UserLoggedInEvent::of(
            $user,
            $command->getPassword(),
            $this->passwordHasher,
        );
    }

    private function getEmail(LoginUserCommand $command): Email
    {
        return Email::fromString($command->getEmail(), $this->validator);
    }

    private function findUser(Email $email): User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['email.email' => $email]) ?? throw new UserNotFoundException(email: $email);
    }
}
