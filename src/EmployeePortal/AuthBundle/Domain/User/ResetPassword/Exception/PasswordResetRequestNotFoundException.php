<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Exception;

use App\EmployeePortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;
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
