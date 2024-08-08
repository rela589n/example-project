<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;

final class UserNotFoundException extends DomainException implements AuthException, InvalidValueException
{
    public function __construct(
        private readonly ?Email $email,
    ) {
        parent::__construct('auth.user.not_found');
    }

    public function getInvalidValue(): ?string
    {
        return $this->email?->getEmail();
    }
}
