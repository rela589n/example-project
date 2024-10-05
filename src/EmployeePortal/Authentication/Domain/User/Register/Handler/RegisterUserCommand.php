<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Handler;

use App\EmployeePortal\Authentication\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\ValueObject\Email\EmailValidationFailedException;
use App\EmployeePortal\Authentication\Domain\ValueObject\Password\PasswordValidationFailedException;
use OpenApi\Attributes\Property;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    #[Property(example: 'email@test.com')]
    #[Capture(exception: EmailAlreadyTakenException::class, condition: ValueExceptionMatchCondition::class)]
    #[Capture(exception: EmailValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    private string $email;

    #[Property(example: 'p@$$w0rd')]
    #[Capture(exception: PasswordValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    private string $password;

    public function __construct(
        string $email,
        string $password,
    ) {
        $this->password = $password;
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
