<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Register\Inbox;

use App\EmployeePortal\Authentication\User\Actions\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\User\Actions\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Email\EmailValidationFailedException;
use App\EmployeePortal\Authentication\User\Password\Password;
use App\EmployeePortal\Authentication\User\Password\PasswordValidationFailedException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as ApiDoc;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use SensitiveParameter;
use Symfony\Component\Uid\Uuid;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    #[ApiDoc\Property(example: 'd0db5712-6ed7-4c88-a0b0-5a0cad43db71')]
    private string $id;

    #[ApiDoc\Property(example: 'email@test.com')]
    #[Capture(exception: EmailAlreadyTakenException::class, condition: ValueExceptionMatchCondition::class)]
    #[Capture(exception: EmailValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    private string $email;

    #[ApiDoc\Property(example: 'p@$$w0rd')]
    #[Capture(exception: PasswordValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    private string $password;

    public function __construct(
        string $id,
        string $email,
        #[SensitiveParameter]
        string $password,
    ) {
        $this->id = $id;
        $this->password = $password;
        $this->email = $email;
    }

    /** This method should not have any business logic. It should all be inside the domain model event */
    public function execute(RegisterUserService $service): void
    {
        $event = $this->getEvent($service);

        $event->execute($service->userRepository);

        // usually command.bus has transactional middleware, hence flush() is not necessarily required
        // (this could be useful for fixtures, when one fixture could register multiple
        // users and then flush them all in one go)

        $service->entityManager->persist($event->getUser());
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }

    private function getEvent(RegisterUserService $service): UserRegisteredEvent
    {
        /**
         * Usage of awaitAnyN() allows us to show all the validation errors at once instead of showing them one by one.
         * This is achieved by exception unwrapper integrated into exceptional validation component.
         *
         * @var Email $email
         * @var Password $password
         */
        [$email, $password] = awaitAnyN(2, [
            async($this->email(...), $service),
            async($this->password(...), $service),
        ]);

        return new UserRegisteredEvent(
            Uuid::fromString($this->id),
            new User(),
            $email,
            $password,
            CarbonImmutable::instance($service->clock->now()),
        );
    }

    private function email(RegisterUserService $service): Email
    {
        return Email::fromString($this->email, $service->validator);
    }

    private function password(RegisterUserService $service): Password
    {
        return Password::fromString($this->password, $service->validator, $service->passwordHasher);
    }
}
