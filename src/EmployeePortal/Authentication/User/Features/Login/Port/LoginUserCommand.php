<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Login\Port;

use App\EmployeePortal\Authentication\User\_Support\Repository\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Email\EmailValidationFailedException;
use App\EmployeePortal\Authentication\User\Features\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Password\PasswordMismatchException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use OpenApi\Attributes as ApiDoc;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Mapper\Validator\Formatter\Item\ViolationList\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Rule\Object\Property\Capture\Condition\Value\ExceptionValueMatchCondition;
use SensitiveParameter;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

#[ExceptionalValidation]
final readonly class LoginUserCommand
{
    #[ApiDoc\Property(example: 'email@test.com')]
    #[Capture(exception: EmailValidationFailedException::class, condition: ExceptionValueMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    #[Capture(exception: UserNotFoundException::class, condition: ExceptionValueMatchCondition::class)]
    private string $email;

    #[ApiDoc\Property(example: 'p@$$w0rd')]
    #[Capture(exception: PasswordMismatchException::class, condition: ExceptionValueMatchCondition::class)]
    private string $password;

    private(set) JWTUser $jwtUser;

    private(set) string $jwtToken;

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

        $this->jwtUser = new JWTUser($user->getId()->toRfc4122(), ['ROLE_USER']);
        $this->jwtToken = $service->tokenManager->create($this->jwtUser);
    }

    private function getUser(LoginUserService $service): User
    {
        return $service->userRepository->findByEmail($this->getEmail($service));
    }

    private function getEmail(LoginUserService $service): Email
    {
        return Email::fromString($this->email, $service->validator);
    }
}
