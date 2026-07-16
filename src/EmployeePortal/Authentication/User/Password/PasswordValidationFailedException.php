<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Password;

use App\EmployeePortal\Authentication\AuthException;
use DomainException;
use PhPhD\ExceptionalMatcher\Rule\Object\Property\Match\Condition\Value\ValueException;
use PhPhD\ExceptionalMatcher\Integration\Validator\Formatter\Embedded\ViolationsEmbeddedException;
use Symfony\Component\Validator\ConstraintViolationListInterface;


final class PasswordValidationFailedException extends DomainException implements AuthException, ValueException, ViolationsEmbeddedException
{
    public function __construct(
        private readonly string $password,
        private readonly ConstraintViolationListInterface $violations,
    ) {
        parent::__construct((string)$this->violations);
    }

    public function getValue(): string
    {
        return $this->password;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
