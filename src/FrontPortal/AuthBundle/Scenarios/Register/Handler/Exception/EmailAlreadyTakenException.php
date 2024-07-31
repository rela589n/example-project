<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Scenarios\Register\Handler\Exception;

use App\FrontPortal\AuthBundle\Domain\Model\ValueObject\Email;
use App\FrontPortal\AuthBundle\Scenarios\Registration\Handler\Exception\InvalidValueException;
use RuntimeException;

final class EmailAlreadyTakenException extends RuntimeException implements InvalidValueException
{
    public function __construct(
        private readonly Email $email,
    ) {
        parent::__construct();
    }

    public function getInvalidValue(): string
    {
        return $this->email->getEmail();
    }
}
