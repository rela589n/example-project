<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Login;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Login\Exception\PasswordMismatchException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\EmailValidationFailedException;
use PhPhD\ExceptionalValidation\Capture;
use SensitiveParameter;

final readonly class LoginUserCommand
{
    public function __construct(
        #[Capture(EmailValidationFailedException::class, condition: 'value', formatter: 'violation_list')]
        #[Capture(UserNotFoundException::class, condition: 'value')]
        private string $email,
        #[Capture(PasswordMismatchException::class, condition: 'value')]
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
