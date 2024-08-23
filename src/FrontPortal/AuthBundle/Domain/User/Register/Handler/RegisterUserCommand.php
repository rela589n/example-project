<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\EmailValidationFailedException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\PasswordValidationFailedException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;

#[ExceptionalValidation]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Capture(
            exception: EmailValidationFailedException::class,
            condition: ValueExceptionMatchCondition::class,
            formatter: ViolationListExceptionFormatter::class,
        )]
        #[Capture(
            exception: EmailAlreadyTakenException::class,
            condition: ValueExceptionMatchCondition::class,
        )]
        private string $email,
        #[Capture(
            exception: PasswordValidationFailedException::class,
            condition: ValueExceptionMatchCondition::class,
            formatter: ViolationListExceptionFormatter::class
        )]
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
