<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Password;

use PhPhD\ExceptionalValidation\Rule\Object\Property\Capture\Condition\Value\ValueException;
use App\EmployeePortal\Authentication\AuthException;
use DomainException;
use SensitiveParameter;

final class PasswordMismatchException extends DomainException implements AuthException, ValueException
{
    public function __construct(
        #[SensitiveParameter]
        private readonly string $password,
    ) {
        parent::__construct('auth.login.password_mismatch');
    }

    public function getValue(): string
    {
        return $this->password;
    }
}
