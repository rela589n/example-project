<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Service;

use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegistration;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\UserRepository;
use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;
use App\EmployeePortal\Authentication\Domain\ValueObject\Password\Password;
use Carbon\CarbonImmutable;
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
final readonly class RegisterUserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        private ValidatorInterface $validator,
        #[Autowire('@=service("security.password_hasher_factory").getPasswordHasher("'.User::class.'")')]
        private PasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        /**
         * Usage of awaitAnyN() allows us to show all the validation errors at once instead of showing them one by one.
         * This is achieved by exception unwrapper integrated into exceptional validation component.
         *
         * @var Email $email
         * @var Password $password
         */
        [$email, $password] = awaitAnyN(2, [
            async($this->email(...), $command),
            async($this->password(...), $command),
        ]);

        $registration = new UserRegistration(
            $id = Uuid::v7(),
            new User($id),
            $email,
            $password,
            CarbonImmutable::instance($this->clock->now()),
        );

        $registration->process($this->userRepository);

        // usually command.bus has transactional middleware, hence flush() is not necessarily required
        // (this could be useful for fixtures, when one fixture could register multiple
        // users and then flush them all in one go)

        $this->entityManager->persist($registration);
        $this->entityManager->flush();

        $this->eventBus->dispatch($registration);
    }

    private function email(RegisterUserCommand $command): Email
    {
        return Email::fromString($this->validator, $command->getEmail());
    }

    private function password(RegisterUserCommand $command): Password
    {
        return Password::fromString($this->validator, $this->passwordHasher, $command->getPassword());
    }
}
