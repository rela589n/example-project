<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Password;

use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Note that in fact if client code has instance of Password, it means that:
 * - at the moment when password has been created (it could've been a year ago), it has been valid
 *
 * The fact that object could not be created in the invalid state completely removes temporal coupling (https://www.pluralsight.com/tech-blog/forms-of-temporal-coupling/)
 */
#[ORM\Embeddable]
final readonly class Password
{
    private function __construct(
        #[ORM\Column]
        private string $hash,
    ) {
    }

    public static function fromString(
        #[SensitiveParameter] string $password,
        ValidatorInterface $validator,
        PasswordHasherInterface $passwordHasher,
    ): self {
        $violationList = $validator->validate($password, new Assert\Sequentially([
            new Assert\NotBlank(),
            new Assert\Length(min: 8, max: 31),
            new Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM),
        ]));

        if (0 !== $violationList->count()) {
            throw new PasswordValidationFailedException($password, $violationList);
        }

        return new self($passwordHasher->hash($password));
    }

    public function verify(string $plainPassword, PasswordHasherInterface $passwordHasher): void
    {
        if (!$passwordHasher->verify($this->getHash(), $plainPassword)) {
            throw new PasswordMismatchException($plainPassword);
        }
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
