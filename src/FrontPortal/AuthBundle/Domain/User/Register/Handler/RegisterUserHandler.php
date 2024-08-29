<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Closure;
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
    private Closure $createEmail;
    private Closure $createPassword;
    private Closure $registerUser;

    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        ValidatorInterface $validator,
        PasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        ClockInterface $clock,
    ) {
        $this->createEmail = Email::fromString($validator);
        $this->createPassword = Password::fromString($validator, $passwordHasher);
        $this->registerUser = UserRegisteredEvent::process($clock, $userRepository);
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        /**
         * @var Email $email
         * @var Password $password
         */
        [$email, $password] = awaitAnyN(2, [
            async(fn (): Email => ($this->createEmail)($command->getEmail())),
            async(fn (): Password => ($this->createPassword)($command->getPassword())),
        ]);

        $event = ($this->registerUser)(Uuid::v7(), $email, $password);

        $this->entityManager->persist($event->getUser());

        $this->eventBus->dispatch($event);
    }
}
