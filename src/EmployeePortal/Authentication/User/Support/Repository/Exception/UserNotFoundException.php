<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Repository\Exception;

use App\EmployeePortal\Authentication\AuthException;
use App\EmployeePortal\Authentication\User\Email\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Value\ValueException;
use Symfony\Component\Uid\Uuid;

final class UserNotFoundException extends DomainException implements AuthException, ValueException
{
    public function __construct(
        private readonly ?Uuid $id = null,
        private readonly ?Email $email = null,
    ) {
        parent::__construct('auth.user.not_found');
    }

    public function getValue(): ?string
    {
        return $this->id?->toRfc4122()
            ?? $this->email?->getEmail();
    }
}
