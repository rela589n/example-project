<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Password;

use App\EmployeePortal\Authentication\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Formatter\ViolationListException;
use PhPhD\ExceptionalValidation\Model\Condition\Value\ValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class PasswordValidationFailedException extends DomainException implements AuthException, ValueException, ViolationListException
{
    public function __construct(
        private readonly string $password,
        private readonly ConstraintViolationListInterface $violationList,
    ) {
        parent::__construct((string)$this->violationList);
    }

    public function getValue(): string
    {
        return $this->password;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
