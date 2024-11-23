<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Model\Exception;

use App\EmployeePortal\Authentication\Domain\AuthException;
use App\EmployeePortal\Authentication\Domain\User\PasswordReset\PasswordResetRequest;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;

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
