<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register;

use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\EmailValidationFailedException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\PasswordValidationFailedException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Capture(EmailValidationFailedException::class, condition: 'value', formatter: 'violation_list')]
        #[Capture(EmailAlreadyTakenException::class, condition: 'value')]
        private string $email,
        #[Capture(PasswordValidationFailedException::class, condition: 'value', formatter: 'violation_list')]
        private string $password,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
