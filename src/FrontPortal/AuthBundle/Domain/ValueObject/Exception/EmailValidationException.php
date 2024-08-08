<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class EmailValidationException extends DomainException implements AuthException, InvalidValueException, ViolationListException
{
    public function __construct(
        private readonly string $email,
        private readonly ConstraintViolationListInterface $violationList,
    ) {
        parent::__construct('auth.email.invalid');
    }

    public function getInvalidValue(): string
    {
        return $this->email;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
