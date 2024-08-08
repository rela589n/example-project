<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Exception;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\InvalidValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class PasswordValidationException extends DomainException implements AuthException, InvalidValueException, ViolationListException
{
    public function __construct(
        private readonly string $password,
        private readonly ConstraintViolationListInterface $violationList,
    ) {
        parent::__construct('auth.password.invalid');
    }

    public function getInvalidValue(): string
    {
        return $this->password;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
