<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Tests;

use App\EmployeePortal\Authentication\Domain\User\Register\Model\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegisteredEvent;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\UserRepository;
use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;
use App\EmployeePortal\Authentication\Domain\ValueObject\Password\Password;
use Closure;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validation;

/**
 * The best way to cover business logic with unit test is to cover it starting from event::process() method.
 * This way it's not necessary to overreach with mocking for entity manager and event bus that are in fact infrastructural.
 */
#[CoversClass(UserRegisteredEvent::class)]
#[CoversClass(User::class)]
final  class RegisterUserUnitTest extends TestCase
{
    private Closure $registerUser;

    private UserRepository&Stub $userRepository;

    private Closure $email;

    private Closure $password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = Email::fromString(Validation::createValidator());
        $this->password = Password::fromString(Validation::createValidator(), new Pbkdf2PasswordHasher());

        $userRepository = $this->createStub(UserRepository::class);
        $this->userRepository = $userRepository;

        $this->registerUser = UserRegisteredEvent::process(
            new MockClock('2024-08-26 22:01:13'),
            $this->userRepository,
        );
    }

    public function testEmailMustBeUnique(): void
    {
        $this->userRepository->method('isEmailFree')
            ->willReturn(false);

        $this->expectException(EmailAlreadyTakenException::class);

        ($this->registerUser)(
            Uuid::v7(),
            ($this->email)('test@email.com'),
            ($this->password)('jG\Qc_g7;%zE85'),
        );
    }

    public function testUserIsRegisteredSuccessfully(): void
    {
        $this->userRepository->method('isEmailFree')
            ->willReturn(true);

        $event = ($this->registerUser)(
            Uuid::v7(),
            ($this->email)('test@email.com'),
            ($this->password)('jG\Qc_g7;%zE85'),
        );

        $user = $event->getUser();

        self::assertSame('test@email.com', $user->getEmail()->getEmail());
        self::assertSame('UiRp8M0HR2fedmHmsJEX4elDj8Ry3PoPAaBtLZcJe37IzB+L0ISMYg==', $user->getPassword()->getHash());
        self::assertSame('2024-08-26T22:01:13+00:00', $user->getCreatedAt()->toIso8601String());
        self::assertSame('2024-08-26T22:01:13+00:00', $user->getUpdatedAt()->toIso8601String());
    }
}
