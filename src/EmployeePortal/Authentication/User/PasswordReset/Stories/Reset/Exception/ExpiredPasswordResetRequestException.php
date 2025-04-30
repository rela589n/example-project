<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Stories\Reset\Exception;

use PhPhD\ExceptionalValidation\Rule\Object\Property\Capture\Condition\Value\ValueException;
use App\EmployeePortal\Authentication\AuthException;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use DomainException;

final class ExpiredPasswordResetRequestException extends DomainException implements AuthException, ValueException
{
    public function __construct(
        private readonly PasswordResetRequest $request,
    ) {
        parent::__construct('auth.user.password_reset_request.expired');
    }

    public function getValue(): string
    {
        return $this->request->getId()->toRfc4122();
    }
}
