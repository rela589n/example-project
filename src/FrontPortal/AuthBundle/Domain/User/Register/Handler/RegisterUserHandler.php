<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

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
        private EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $createEmail = Email::fromString($this->validator);
        $createPassword = Password::fromString($this->validator, $this->passwordHasher);
        $registerUser = UserRegisteredEvent::process($this->clock, $this->userRepository);

        /**
         * Usage of awaitAnyN() allows us to show all the validation errors at once instead of showing them one by one.
         * This is achieved by exception unwrapper integrated into exceptional validation component.
         *
         * @var Email $email
         * @var Password $password
         */
        [$email, $password] = awaitAnyN(2, [
            async(fn (): Email => $createEmail($command->getEmail())),
            async(fn (): Password => $createPassword($command->getPassword())),
        ]);

        $event = $registerUser(Uuid::v7(), $email, $password);

        // usually command.bus has transactional middleware, hence flush() is not needed
        // also this could be useful for fixtures, when one fixture could register multiple
        // users and then flush them all in one go

        $this->entityManager->persist($event->getUser());

        $this->eventBus->dispatch($event);
    }
}
