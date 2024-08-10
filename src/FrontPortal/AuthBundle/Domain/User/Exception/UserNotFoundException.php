<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;
use Symfony\Component\Uid\Uuid;

final class UserNotFoundException extends DomainException implements AuthException, InvalidValueException
{
    public function __construct(
        private readonly ?Uuid $id = null,
        private readonly ?Email $email = null,
    ) {
        parent::__construct('auth.user.not_found');
    }

    public function getInvalidValue(): ?string
    {
        return $this->id?->toRfc4122()
            ?? $this->email?->getEmail();
    }
}
