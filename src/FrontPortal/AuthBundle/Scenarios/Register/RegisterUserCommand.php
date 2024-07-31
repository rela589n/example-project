<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Scenarios\Register;

use App\FrontPortal\AuthBundle\Scenarios\Register\Handler\Exception\EmailAlreadyTakenException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Capture(ValidationFailedException::class, 'Invalid email', when: [self::class, 'isEmailError'])]
        #[Capture(EmailAlreadyTakenException::class, 'auth.registration.email_already_taken', [self::class, 'isThisEmailTaken'])]
        private string $email,

        #[Capture(ValidationFailedException::class, 'Weak password', when: [self::class, 'isPasswordError'])]
        private string $password,
    ) {
    }

    public function isThisEmailTaken(EmailAlreadyTakenException $exception): bool
    {
        return $exception->getEmail()->getEmail() === $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /** @internal */
    public function isEmailError(ValidationFailedException $exception): bool
    {
        return $exception->getValue() === $this->email;
    }

    /** @internal */
    public function isPasswordError(ValidationFailedException $exception): bool
    {
        return $exception->getValue() === $this->password;
    }
}
