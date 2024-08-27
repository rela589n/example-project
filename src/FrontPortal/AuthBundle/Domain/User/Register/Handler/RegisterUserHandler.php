<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserHandler
{
    public function __construct(
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ClockInterface $clock,
        private EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $event = $this->processEvent($command);

        $this->entityManager->persist($event->getUser());

        $this->eventBus->dispatch($event);
    }

    private function processEvent(RegisterUserCommand $command): UserRegisteredEvent
    {
        /**
         * @var Email $email
         * @var Password $password
         */
        [$email, $password] = awaitAnyN(2, [
            async(fn (): Email => $this->getEmail($command)),
            async(fn (): Password => $this->getPassword($command)),
        ]);

        if (!$this->emailIsFree($email)) {
            throw new EmailAlreadyTakenException($email);
        }

        return UserRegisteredEvent::process(Uuid::v7(), $email, $password, $this->clock->now());
    }

    private function getEmail(RegisterUserCommand $command): Email
    {
        return Email::fromString($command->getEmail(), $this->validator);
    }

    private function getPassword(RegisterUserCommand $command): Password
    {
        return Password::fromString($command->getPassword(), $this->validator, $this->passwordHasher);
    }

    private function emailIsFree(Email $email): bool
    {
        try {
            $this->userRepository->findByEmail($email);

            return false;
        } catch (UserNotFoundException) {
            return true;
        }
    }
}
