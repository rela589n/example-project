<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\ValueObject\Email;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(Email::class)]
final class EmailUnitTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = Validation::createValidator();
    }

    public function testEmailMustNotBeBlank(): void
    {
        $this->expectException(EmailValidationException::class);
        $this->expectExceptionMessage(
            'This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)'
        );

        Email::fromString('', $this->validator);
    }

    #[DataProvider('untrimmedEmailsProvider')]
    public function testEmailMustBeTrimmedBeforeHand(string $untrimmedEmail): void
    {
        $this->expectException(EmailValidationException::class);
        $this->expectExceptionMessage(
            'This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)'
        );

        Email::fromString($untrimmedEmail, $this->validator);
    }

    #[DataProvider('invalidEmailsProvider')]
    public function testEmailMustBeValid(string $invalidEmail): void
    {
        $this->expectException(EmailValidationException::class);
        $this->expectExceptionMessage(
            'This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)'
        );

        Email::fromString($invalidEmail, $this->validator);
    }

    public function testValidEmail(): void
    {
        $email = Email::fromString('example@test.com', $this->validator);

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
