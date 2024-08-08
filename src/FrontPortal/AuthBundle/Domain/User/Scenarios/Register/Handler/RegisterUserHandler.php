<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\RegisterUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserHandler
{
    public function __construct(
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $event = UserRegisteredEvent::unwrap(
            static fn () => new User(),
            $this->getEmail($command->getEmail()),
            $this->getPassword($command->getPassword()),
        );

        if (!$this->isEmailFree($event->getEmail())) {
            throw new EmailAlreadyTakenException($event->getEmail());
        }

        // $event.process() is called by the first handler
        $this->eventBus->dispatch($event);

        $this->entityManager->persist($event->getUser());
    }

    private function getEmail(string $email): Closure
    {
        return static fn () => Email::fromUserInput($email, $this->validator);
    }

    private function getPassword(string $password): Closure
    {
        return static fn () => Password::fromUserInput($password, $this->validator, $this->passwordHasher);
    }

    private function isEmailFree(Email $email): bool
    {
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email.email' => $email->getEmail()]);

        return null === $existingUser;
    }
}
