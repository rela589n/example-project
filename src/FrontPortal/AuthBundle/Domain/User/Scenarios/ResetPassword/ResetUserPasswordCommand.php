<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword;

use App\FrontPortal\AuthBundle\Domain\User\Exception\ExpiredPasswordResetRequestException;
use App\FrontPortal\AuthBundle\Domain\User\Exception\PasswordResetRequestNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
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
