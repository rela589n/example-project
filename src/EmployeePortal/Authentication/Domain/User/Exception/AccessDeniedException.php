<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Exception;

use App\EmployeePortal\Authentication\Domain\AuthException;
use App\EmployeePortal\Authentication\Domain\User\User;
use DomainException;

final class AccessDeniedException extends DomainException implements AuthException
{
    public function __construct(
        private readonly User $user,
    ) {
        parent::__construct('auth.user.access_denied');
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
