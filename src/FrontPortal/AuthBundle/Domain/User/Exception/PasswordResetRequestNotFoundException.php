<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;
use Symfony\Component\Uid\Uuid;

final class PasswordResetRequestNotFoundException extends DomainException implements AuthException, InvalidValueException
{
    public function __construct(
        private readonly ?Uuid $id = null,
    ) {
        parent::__construct('auth.user.password_reset_request.not_found');
    }

    public function getInvalidValue(): ?string
    {
        return $this->id?->toRfc4122();
    }
}
