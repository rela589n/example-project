<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Tests;

use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(Password::class)]
final class PasswordUnitTest extends TestCase
{
    private ValidatorInterface $validator;

    private PasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = Validation::createValidator();
        $this->passwordHasher = new Pbkdf2PasswordHasher();
    }

    public function testPasswordMustNotBeBlank(): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)'
        );

        Password::fromUserInput('', $this->validator, $this->passwordHasher);
    }

    public function testPasswordMustBeAtLeast8CharactersLong(): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value is too short. It should have 8 characters or more. (code 9ff3fdc4-b214-49db-8718-39c315e33d45)'
        );

        Password::fromUserInput('1234567', $this->validator, $this->passwordHasher);
    }

    public function testPasswordMustNotBeLongerThan31CharactersLong(): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value is too long. It should have 31 characters or less. (code d94b19cc-114f-4f44-9cc4-4138e80a87b9)'
        );

        Password::fromUserInput(str_repeat('@', 32), $this->validator, $this->passwordHasher);
    }

    #[DataProvider('weakPasswordsProvider')]
    public function testPasswordMustNotBeWeak(string $weakPassword): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage(
            'The password strength is too low. Please use a stronger password. (code 4234df00-45dd-49a4-b303-a75dbf8b10d8)'
        );

        Password::fromUserInput($weakPassword, $this->validator, $this->passwordHasher);
    }

    public function testValidPassword(): void
    {
        $password = Password::fromUserInput('58Ez%;7g_cQ\Gj', $this->validator, $this->passwordHasher);

        self::assertSame('LoxjdBDi5g63XQ/XdnFYsPjgPHpq7W5z3J861pecrCAGJspDK4ddwA==', $password->getHash());
    }

    public static function weakPasswordsProvider(): array
    {
        return [
            'p@$$w0rd',
            'v/W29O4F8-tf',
        ];
    }
}
