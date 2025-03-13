<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Login\Inbox;

use App\EmployeePortal\Authentication\User\Actions\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Email\EmailValidationFailedException;
use App\EmployeePortal\Authentication\User\Password\PasswordMismatchException;
use App\EmployeePortal\Authentication\User\Support\Repository\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use SensitiveParameter;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

#[ExceptionalValidation]
final readonly class LoginUserCommand
{
    #[Capture(exception: EmailValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    #[Capture(exception: UserNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    private string $email;

    #[Capture(exception: PasswordMismatchException::class, condition: ValueExceptionMatchCondition::class)]
    private string $password;

    private JWTUser $jwtUser;

    private string $jwtToken;

    public function __construct(
        string $email,
        #[SensitiveParameter]
        string $password,
    ) {
        $this->email = $email;
        $this->password = $password;
    }

    /** @throws ExceptionInterface */
    public function process(LoginUserService $service): void
    {
        $event = new UserLoggedInEvent(
            Uuid::v7(),
            $user = $this->getUser($service),
            CarbonImmutable::instance($service->clock->now()),
        );

        $event->process($this->password, $service->passwordHasher);

        $service->entityManager->flush();
        $service->eventBus->dispatch($event);

        $this->jwtUser = new JWTUser($user->getId()->toRfc4122());
        $this->jwtToken = $service->tokenManager->create($this->jwtUser);
    }

    private function getUser(LoginUserService $service): User
    {
        return $service->userRepository->findByEmail($this->getEmail($service));
    }

    private function getEmail(LoginUserService $service): Email
    {
        return Email::fromString($service->validator, $this->email);
    }

    public function getJwtUser(): JWTUser
    {
        return $this->jwtUser;
    }

    public function getJwtToken(): string
    {
        return $this->jwtToken;
    }
}
