<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Handler;

use Amp\Future;
use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\LoginUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Amp\async;

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
        $event = UserLoggedInEvent::of(
            $this->findUser($this->getEmail($command)),
            $command->getPassword(),
            $this->passwordHasher,
        );

        $this->eventBus->dispatch($event);

        $this->entityManager->persist($event);
    }

    private function findUser(Future $email): Future
    {
        return async(function () use ($email) {
            $emailValue = $email->await();

            return $this->entityManager->getRepository(User::class)
                ->findOneBy(['email.email' => $emailValue]) ?? throw new UserNotFoundException(email: $emailValue);
        });
    }

    private function getEmail(LoginUserCommand $command): Future
    {
        return async(fn () => Email::fromUserInput($command->getEmail(), $this->validator));
    }
}
