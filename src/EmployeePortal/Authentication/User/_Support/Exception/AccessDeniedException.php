<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\_Support\Exception;

use App\EmployeePortal\Authentication\AuthException;
use App\EmployeePortal\Authentication\User\User;
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
