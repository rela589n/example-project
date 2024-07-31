<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\Model\ValueObject;

use SensitiveParameter;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Password
{
    private function __construct(
        private string $hash,
    ) {
    }

    public static function fromUserInput(
        ValidatorInterface $validator,
        PasswordHasherInterface $passwordHasher,
        #[SensitiveParameter]
        string $password,
    ): self {
        $validate = Validation::createCallable(
            $validator,
            new Assert\NotBlank(),
            new Assert\Length(min: 8),
            new Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_VERY_STRONG),
        );

        $validate($password);

        return new self($passwordHasher->hash($password));
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
