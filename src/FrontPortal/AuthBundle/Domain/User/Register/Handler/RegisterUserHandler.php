<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
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
            async(fn (): Email => $command->getEmail($this->validator)),
            async(fn (): Password => $command->getPassword($this->validator, $this->passwordHasher)),
        ]);

        return UserRegisteredEvent::process(
            $email,
            $password,
            $this->entityManager->getRepository(User::class),
        );
    }

}
