<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Email;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Email
{
    private function __construct(
        private string $email,
    ) {
    }

    public static function fromString(string $email, ValidatorInterface $validator): self
    {
        // Value-object must cover the basic validation constraints to be easily unit-tested.
        // Additional validation logic (like uniqueness) must be implemented in the service (Handler)
        // and tested with integration test.

        $violationList = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (0 !== $violationList->count()) {
            throw new EmailValidationException($email, $violationList);
        }

        return new self($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
