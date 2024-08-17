<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Reset\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\PasswordResetRequest;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;

final class ExpiredPasswordResetRequestException extends DomainException implements AuthException, ValueException
{
    public function __construct(
        private readonly PasswordResetRequest $request,
    ) {
        parent::__construct('auth.user.password_reset_request.expired');
    }

    public function getValue(): string
    {
        return $this->request->getId()->toRfc4122();
    }
}
