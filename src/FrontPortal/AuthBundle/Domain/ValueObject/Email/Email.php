<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Email;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Email
{
    private function __construct(
        private string $email,
    ) {
    }

    public static function fromString(
        string $email,
        ValidatorInterface $validator,
    ): self {
        // Value-object must convey the basic validation rules in order to enforce invariants and be easily unit-tested.
        // Additional validation logic (like email uniqueness) should be implemented in the service (Handler)
        // and tested with integration test.

        $violationList = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (0 !== $violationList->count()) {
            throw new EmailValidationFailedException($email, $violationList);
        }

        return new self($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
