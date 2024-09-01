<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Tests;

use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\Register\Handler\RegisterUserCommand;
use App\FrontPortal\AuthBundle\Domain\User\Register\Handler\RegisterUserHandler;
use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Symfony\Component\Validator\Validation;

/**
 * Handlers are full of infrastructure code, - therefore they are hard to unit test.
 * This is just an example of what is better not to do.
 * @see RegisterUserUnitTest as a better alternative
 */
#[CoversClass(UserRegisteredEvent::class)]
#[CoversClass(User::class)]
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
            $this->entityManager,
            $this->eventBus,
            Validation::createValidator(),
            new Pbkdf2PasswordHasher(),
            $this->userRepository,
            new MockClock('2024-08-26 22:01:13'),
        );
    }

    public function testEmailMustBeUnique(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn($this->createMock(User::class));

        $command = new RegisterUserCommand('test@email.com', 'jG\Qc_g7;%zE85');

        $this->expectException(EmailAlreadyTakenException::class);

        $this->registerUserHandler->__invoke($command);
    }

    public function testUserIsRegisteredSuccessfully(): void
    {
        $command = new RegisterUserCommand('test@email.com', 'jG\Qc_g7;%zE85');

        $this->userRepository->method('isEmailFree')
            ->willReturn(true);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (User $user) {
                self::assertSame('test@email.com', $user->getEmail()->getEmail());
                self::assertSame('UiRp8M0HR2fedmHmsJEX4elDj8Ry3PoPAaBtLZcJe37IzB+L0ISMYg==', $user->getPassword()->getHash());
                self::assertSame('2024-08-26T22:01:13+00:00', $user->getCreatedAt()->toIso8601String());
                self::assertSame('2024-08-26T22:01:13+00:00', $user->getUpdatedAt()->toIso8601String());
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
