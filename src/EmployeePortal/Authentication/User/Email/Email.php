<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Email;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[ORM\Embeddable]
final readonly class Email
{
    private function __construct(
        #[ORM\Column(nullable: false)]
        private string $email,
    ) {
    }

    public static function fromString(string $email, ValidatorInterface $validator): self
    {
        // Value-object must convey the basic validation rules in order to enforce invariants provided by the business.
        // In addition, this approach makes these invariants easily unit-tested.
        // Ad-hoc infrastructure-related validation logic (like email uniqueness)
        // should be implemented in each particular domain service outside the value-object.

        $violationList = $validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (0 !== $violationList->count()) {
            throw new EmailValidationFailedException($email, $violationList);
        }

        return new self($email);
    }

    public function toString(): string
    {
        return $this->email;
    }
}
