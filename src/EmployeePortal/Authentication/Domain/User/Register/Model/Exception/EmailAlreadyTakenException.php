<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Model\Exception;

use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;

final class EmailAlreadyTakenException extends DomainException implements ValueException
{
    public function __construct(
        private readonly Email $email,
    ) {
        parent::__construct('auth.registration.email_already_taken');
    }

    public function getValue(): string
    {
        return $this->email->getEmail();
    }
}
