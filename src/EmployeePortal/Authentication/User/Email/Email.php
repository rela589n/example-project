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
        // Value-object must convey the basic validation rules in order to enforce invariants provided by business.
        // In addition, this approach makes invariants easily unit-tested.
        // Additional infrastructure-related validation logic (like email uniqueness)
        // should be implemented in each particular scenario outside value-object.

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
