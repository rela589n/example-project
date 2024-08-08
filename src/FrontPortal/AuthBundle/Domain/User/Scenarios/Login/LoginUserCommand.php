<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Exception\PasswordMismatchException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Exception\EmailValidationException;
use PhPhD\ExceptionalValidation\Capture;
use SensitiveParameter;

final readonly class LoginUserCommand
{
    public function __construct(
        #[Capture(EmailValidationException::class, condition: 'invalid_value', formatter: 'violation_list')]
        #[Capture(UserNotFoundException::class, condition: 'invalid_value')]
        private string $email,
        #[Capture(PasswordMismatchException::class, condition: 'invalid_value')]
        #[SensitiveParameter]
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
