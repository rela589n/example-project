<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\RegisterUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Amp\async;
use function Amp\Future\awaitAnyN;

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
        $event = $this->createEvent($command);

        if (!$this->isEmailFree($event->getEmail())) {
            throw new EmailAlreadyTakenException($event->getEmail());
        }

        // $event.process() is called by the first handler
        $this->eventBus->dispatch($event);

        $this->entityManager->persist($event->getUser());
    }

    private function createEvent(RegisterUserCommand $command): UserRegisteredEvent
    {
        [$email, $password] = awaitAnyN(2, [
            async(fn (): Email => $this->getEmail($command->getEmail())),
            async(fn (): Password => $this->getPassword($command->getPassword())),
        ]);

        return new UserRegisteredEvent(new User(), $email, $password);
    }

    private function getEmail(string $email): Email
    {
        return Email::fromUserInput($email, $this->validator);
    }

    private function getPassword(string $password): Password
    {
        return Password::fromUserInput($password, $this->validator, $this->passwordHasher);
    }

    private function isEmailFree(Email $email): bool
    {
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email.email' => $email->getEmail()]);

        return null === $existingUser;
    }
}
