<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Login\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
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
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private PasswordHasherInterface $passwordHasher,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(LoginUserCommand $command): void
    {
        $event = $this->processEvent($command);

        $this->eventBus->dispatch($event);

        $this->entityManager->persist($event);
    }

    private function processEvent(LoginUserCommand $command): UserLoggedInEvent
    {
        return UserLoggedInEvent::process(
            $this->getUser($command),
            $command->getPassword(),
            $this->passwordHasher,
        );
    }

    private function getUser(LoginUserCommand $command): User
    {
        $email = Email::fromString($command->getEmail(), $this->validator);

        return $this->userRepository->findByEmail($email);
    }
}
