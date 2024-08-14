<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\ResetPassword\Reset;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\ResetPassword\Exception\PasswordResetRequestNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\ResetPassword\Reset\Exception\ExpiredPasswordResetRequestException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use Symfony\Component\Validator\Constraints as Assert;

#[ExceptionalValidation]
final readonly class ResetUserPasswordCommand
{
    public function __construct(
        #[Assert\Uuid]
        #[Capture(UserNotFoundException::class)]
        private string $userId,
        #[Assert\Uuid]
        #[Capture(PasswordResetRequestNotFoundException::class)]
        #[Capture(ExpiredPasswordResetRequestException::class)]
        private string $passwordResetRequestId,
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPasswordResetRequestId(): string
    {
        return $this->passwordResetRequestId;
    }
}