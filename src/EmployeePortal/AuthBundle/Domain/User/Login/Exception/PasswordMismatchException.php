<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\Login\Exception;

use App\EmployeePortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;
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