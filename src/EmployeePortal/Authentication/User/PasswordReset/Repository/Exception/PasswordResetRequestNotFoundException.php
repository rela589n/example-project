<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Repository\Exception;

use App\EmployeePortal\Authentication\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Value\ValueException;
use Symfony\Component\Uid\Uuid;

final class PasswordResetRequestNotFoundException extends DomainException implements AuthException, ValueException
{
    public function __construct(
        private readonly ?Uuid $id = null,
    ) {
        parent::__construct('auth.user.password_reset_request.not_found');
    }

    public function getValue(): ?string
    {
        return $this->id?->toRfc4122();
    }
}
