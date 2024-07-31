<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\Model\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Email
{
    private function __construct(
        private string $email,
    ) {
    }

    public static function fromUserInput(ValidatorInterface $validator, string $email): self
    {
        $validate = Validation::createCallable($validator, new Assert\NotBlank(), new Assert\Email());

        $validate($email);

        return new self($email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
