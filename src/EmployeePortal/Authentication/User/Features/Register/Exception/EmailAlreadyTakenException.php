<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Exception;

use App\EmployeePortal\Authentication\User\Email\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Rule\Object\Property\Capture\Condition\Value\ValueException;

final class EmailAlreadyTakenException extends DomainException implements ValueException
{
    public function __construct(
        private readonly Email $email,
    ) {
        parent::__construct('auth.registration.email_already_taken');
    }

    public function getValue(): string
    {
        return $this->email->toString();
    }
}
