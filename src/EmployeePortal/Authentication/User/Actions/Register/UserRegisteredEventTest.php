<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Register;

use App\EmployeePortal\Authentication\User\Actions\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Password\Password;
use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validation;

/**
 * The best way to cover business logic with unit test is to cover it starting from event::process() method.
 * This way it's not necessary to overreach with mocking for entity manager and event bus that are in fact infrastructural.
 */
#[CoversClass(UserRegisteredEvent::class)]
#[CoversClass(User::class)]
final class UserRegisteredEventTest extends TestCase
{
    private UserRepository&Stub $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createStub(UserRepository::class);
    }

    public function testEmailMustBeUnique(): void
    {
        $this->userRepository->method('isEmailFree')
            ->willReturn(false);

        $this->expectException(EmailAlreadyTakenException::class);

        $this->registerUser();
    }

    public function testSuccessfulUserRegistration(): void
    {
        $this->userRepository->method('isEmailFree')
            ->willReturn(true);

        $user = $this->registerUser();

        self::assertSame('test@email.com', $user->getEmail()->getEmail());
        self::assertSame('UiRp8M0HR2fedmHmsJEX4elDj8Ry3PoPAaBtLZcJe37IzB+L0ISMYg==', $user->getPassword()->getHash());
        self::assertSame('2024-08-26T22:01:13+00:00', $user->getCreatedAt()->toIso8601String());
        self::assertSame('2024-08-26T22:01:13+00:00', $user->getUpdatedAt()->toIso8601String());
    }

    private function registerUser(): User
    {
        $registration = new UserRegisteredEvent(
            Uuid::v7(),
            new User(),
            $this->email(),
            $this->password(),
            CarbonImmutable::parse('2024-08-26 22:01:13'),
        );

        $registration->execute($this->userRepository);

        return $registration->getUser();
    }

    private function email(): Email
    {
        return Email::fromString('test@email.com', Validation::createValidator());
    }

    private function password(): Password
    {
        return Password::fromString('jG\Qc_g7;%zE85', Validation::createValidator(), new Pbkdf2PasswordHasher());
    }
}
