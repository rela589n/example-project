<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\Handler\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Exception\EmailValidationException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Exception\PasswordValidationException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Capture(EmailValidationException::class, condition: 'invalid_value', formatter: 'violation_list')]
        #[Capture(EmailAlreadyTakenException::class, condition: 'invalid_value')]
        private string $email,

        #[Capture(PasswordValidationException::class, condition: 'invalid_value', formatter: 'violation_list')]
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
