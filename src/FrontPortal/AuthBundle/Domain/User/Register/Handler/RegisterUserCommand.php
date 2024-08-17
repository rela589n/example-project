<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\EmailValidationFailedException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\PasswordValidationFailedException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Capture(EmailValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
        #[Capture(EmailAlreadyTakenException::class, condition: ValueExceptionMatchCondition::class)]
        private string $email,
        #[Capture(PasswordValidationFailedException::class, condition: ValueExceptionMatchCondition::class, formatter: ViolationListExceptionFormatter::class)]
        private string $password,
    ) {
    }

    public function getEmail(ValidatorInterface $validator): Email
    {
        return Email::fromString($this->email, $validator);
    }

    public function getPassword(ValidatorInterface $validator, PasswordHasherInterface $passwordHasher): Password
    {
        return Password::fromString($this->password, $validator, $passwordHasher);
    }
}
