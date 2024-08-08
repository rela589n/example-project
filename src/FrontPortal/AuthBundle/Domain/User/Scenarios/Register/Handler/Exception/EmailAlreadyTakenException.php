<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Handler\Exception;

use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;

final class EmailAlreadyTakenException extends DomainException implements InvalidValueException
{
    public function __construct(
        private readonly Email $email,
    ) {
        parent::__construct('auth.registration.email_already_taken');
    }

    public function getInvalidValue(): string
    {
        return $this->email->getEmail();
    }
}
