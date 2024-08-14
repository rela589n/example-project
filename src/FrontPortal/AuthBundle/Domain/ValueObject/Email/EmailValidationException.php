<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Email;

use App\FrontPortal\AuthBundle\Domain\AuthException;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Exception\ViolationListException;
use DomainException;
use PhPhD\ExceptionalValidation\Model\Condition\Exception\ValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class EmailValidationException extends DomainException implements AuthException, ValueException, ViolationListException
{
    public function __construct(
        private readonly string $email,
        private readonly ConstraintViolationListInterface $violationList,
    ) {
        parent::__construct();
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
