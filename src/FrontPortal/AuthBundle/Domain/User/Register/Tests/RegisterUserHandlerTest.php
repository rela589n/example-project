<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Tests;

use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\Register\Handler\RegisterUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Register\Handler\RegisterUserHandler;
use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Symfony\Component\Validator\Validation;

/**
 * @covers \App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent
 * @covers \App\FrontPortal\AuthBundle\Domain\User\User
 */
final class RegisterUserHandlerTest extends TestCase
{
    private UserRepository&Stub $userRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private MessageBusInterface&MockObject $eventBus;
    private RegisterUserHandler $registerUserHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createStub(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);

        $this->registerUserHandler = new RegisterUserHandler(
            Validation::createValidator(),
            new Pbkdf2PasswordHasher(),
            $this->userRepository,
            new MockClock('2024-08-26 22:01:13'),
            $this->entityManager,
            $this->eventBus,
        );
    }

    public function testEmailMustBeUnique(): void
    {
        $command = new RegisterUserCommand('test@email.com', 'jG\Qc_g7;%zE85');

        $this->userRepository->method('findByEmail')
            ->willReturn($this->createMock(User::class));

        $this->expectException(EmailAlreadyTakenException::class);

        $this->registerUserHandler->__invoke($command);
    }

    public function testUserIsRegisteredSuccessfully(): void
    {
        $command = new RegisterUserCommand('test@email.com', 'jG\Qc_g7;%zE85');

        $this->userRepository->method('findByEmail')
            ->willReturnCallback(static fn (Email $email) => throw new UserNotFoundException(email: $email));

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (User $user) {
                self::assertSame('test@email.com', $user->getEmail()->getEmail());
                self::assertSame('UiRp8M0HR2fedmHmsJEX4elDj8Ry3PoPAaBtLZcJe37IzB+L0ISMYg==', $user->getPassword()->getHash());
                self::assertSame('2024-08-26T22:01:13+00:00', $user->getCreatedAt()->toIso8601String());
            });

        $this->eventBus->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (object $event) {
                self::assertInstanceOf(UserRegisteredEvent::class, $event);

                return Envelope::wrap($event);
            });

        $this->registerUserHandler->__invoke($command);
    }
}
