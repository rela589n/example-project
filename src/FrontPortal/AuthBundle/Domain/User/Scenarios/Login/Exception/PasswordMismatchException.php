<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;
use SensitiveParameter;

final class PasswordMismatchException extends DomainException implements AuthException, InvalidValueException
{
    public function __construct(
        #[SensitiveParameter]
        private readonly string $password,
    ) {
        parent::__construct('auth.login.password_mismatch');
    }

    public function getInvalidValue(): string
    {
        return $this->password;
    }
}
