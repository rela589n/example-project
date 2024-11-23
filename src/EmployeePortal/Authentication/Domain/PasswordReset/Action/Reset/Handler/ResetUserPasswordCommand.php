<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Handler;

use App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Model\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\Domain\PasswordReset\Domain\Exception\PasswordResetRequestNotFoundException;
use App\EmployeePortal\Authentication\Domain\User\User\Exception\UserNotFoundException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use Symfony\Component\Uid\Uuid;

#[ExceptionalValidation]
final readonly class ResetUserPasswordCommand
{
    #[Capture(UserNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    private string $userId;

    #[Capture(PasswordResetRequestNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    #[Capture(ExpiredPasswordResetRequestException::class, condition: ValueExceptionMatchCondition::class)]
    private string $passwordResetRequestId;

    public function __construct(
        string $userId,
        string $passwordResetRequestId,
    ) {
        $this->userId = $userId;
        $this->passwordResetRequestId = $passwordResetRequestId;
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
