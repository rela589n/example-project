<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject;

use App\FrontPortal\AuthBundle\Domain\ValueObject\Exception\PasswordValidationException;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Password
{
    private function __construct(
        private string $hash,
    ) {
    }

    public static function fromUserInput(
        #[SensitiveParameter]
        string $password,
        ValidatorInterface $validator,
        PasswordHasherInterface $passwordHasher,
    ): self {
        $violationList = $validator->validate($password, new Assert\Sequentially([
            new Assert\NotBlank(),
            new Assert\Length(min: 8, max: 31),
            new Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM),
        ]));

        if (0 !== $violationList->count()) {
            throw new PasswordValidationException($password, $violationList);
        }

        return new self($passwordHasher->hash($password));
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
