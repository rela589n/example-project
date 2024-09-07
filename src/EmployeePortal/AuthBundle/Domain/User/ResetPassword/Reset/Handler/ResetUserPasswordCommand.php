<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Reset\Handler;

use App\EmployeePortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Exception\PasswordResetRequestNotFoundException;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Reset\Exception\ExpiredPasswordResetRequestException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use Symfony\Component\Uid\Uuid;

#[ExceptionalValidation]
final readonly class ResetUserPasswordCommand
{
    public function __construct(
        #[Capture(UserNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
        private string $userId,
        #[Capture(PasswordResetRequestNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
        #[Capture(ExpiredPasswordResetRequestException::class, condition: ValueExceptionMatchCondition::class)]
        private string $passwordResetRequestId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function getPasswordResetRequestId(): Uuid
    {
        return Uuid::fromString($this->passwordResetRequestId);
    }
}
