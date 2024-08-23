<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Login\Handler;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Login\Exception\PasswordMismatchException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\EmailValidationFailedException;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Formatter\ViolationListExceptionFormatter;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use SensitiveParameter;

#[ExceptionalValidation]
final readonly class LoginUserCommand
{
    public function __construct(
        #[Capture(
            exception: EmailValidationFailedException::class,
            condition: ValueExceptionMatchCondition::class,
            formatter: ViolationListExceptionFormatter::class,
        )]
        #[Capture(
            exception: UserNotFoundException::class,
            condition: ValueExceptionMatchCondition::class,
        )]
        private string $email,
        #[Capture(
            exception: PasswordMismatchException::class,
            condition: ValueExceptionMatchCondition::class,
        )]
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
