<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\ValueObject\Email;

use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

#[CoversClass(Email::class)]
final class EmailUnitTest extends TestCase
{
    /** @var Closure(string $email): Email */
    private Closure $createEmail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createEmail = Email::fromString(Validation::createValidator());
    }

    public function testEmailMustNotBeBlank(): void
    {
        $this->expectException(EmailValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)'
        );

        ($this->createEmail)('');
    }

    #[DataProvider('untrimmedEmailsProvider')]
    public function testEmailMustBeTrimmedBeforehand(string $untrimmedEmail): void
    {
        $this->expectException(EmailValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)'
        );

        ($this->createEmail)($untrimmedEmail);
    }

    #[DataProvider('invalidEmailsProvider')]
    public function testEmailMustBeValidEmail(string $invalidEmail): void
    {
        $this->expectException(EmailValidationFailedException::class);
        $this->expectExceptionMessage(
            'This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)'
        );

        ($this->createEmail)($invalidEmail);
    }

    public function testValidEmail(): void
    {
        $email = ($this->createEmail)('example@test.com');

        self::assertSame('example@test.com', $email->getEmail());
    }

    public static function untrimmedEmailsProvider(): array
    {
        return [
            [' example@test.com'],
            ['example@test.com '],
        ];
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            ['@test.com'],
            ['a@.aa'],
            ['@aa.ua'],
            ['aa.@ua'],
        ];
    }
}
