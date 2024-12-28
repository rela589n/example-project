<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model;

use App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\Email\Email;
use App\EmployeePortal\Authentication\Domain\User\Password\Password;
use App\EmployeePortal\Authentication\Domain\User\User;
use Carbon\CarbonImmutable;
use EmployeePortal\Authentication\Domain\User\Support\Repository\UserRepository;
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
            $id = Uuid::v7(),
            new User($id),
            $this->email(),
            $this->password(),
            CarbonImmutable::parse('2024-08-26 22:01:13'),
        );

        $registration->execute($this->userRepository);

        return $registration->getUser();
    }

    private function email(): Email
    {
        return Email::fromString(Validation::createValidator(), 'test@email.com');
    }

    private function password(): Password
    {
        return Password::fromString(Validation::createValidator(), new Pbkdf2PasswordHasher(), 'jG\Qc_g7;%zE85');
    }
}
