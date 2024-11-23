<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Email;

use App\EmployeePortal\Authentication\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Formatter\ViolationListException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class EmailValidationFailedException extends DomainException implements AuthException, ValueException, ViolationListException
{
    public function __construct(
        private readonly string $email,
        private readonly ConstraintViolationListInterface $violationList,
    ) {
        parent::__construct((string)$this->violationList);
    }

    public function getValue(): string
    {
        return $this->email;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}