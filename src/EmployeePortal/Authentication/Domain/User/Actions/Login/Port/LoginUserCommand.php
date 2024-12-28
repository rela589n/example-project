<?php

declare(strict_types=1);

namespace EmployeePortal\Authentication\Domain\User\Actions\Login\Port;

use App\EmployeePortal\Authentication\Domain\User\Email\EmailValidationFailedException;
use EmployeePortal\Authentication\Domain\User\Actions\Login\Exception\PasswordMismatchException;
use EmployeePortal\Authentication\Domain\User\Support\Exception\UserNotFoundException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use SensitiveParameter;

#[ExceptionalValidation]
final readonly class LoginUserCommand
{
    #[Capture(exception: EmailValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
    #[Capture(exception: UserNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    private string $email;

    #[Capture(exception: PasswordMismatchException::class, condition: ValueExceptionMatchCondition::class)]
    private string $password;

    public function __construct(
        string $email,
        #[SensitiveParameter]
        string $password,
    ) {
        $this->email = $email;
        $this->password = $password;
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
