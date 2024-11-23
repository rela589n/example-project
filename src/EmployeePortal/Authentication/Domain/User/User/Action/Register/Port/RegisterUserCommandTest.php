<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Action\Register\Port;

use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Register\UserRegistrationEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Register\UserRegistrationEventTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Handlers are full of infrastructure code, - therefore they are hard to unit test.
 * This is just an example of what is better not to do.
 * @see UserRegistrationEventTest as a better alternative
 */
#[CoversClass(UserRegistrationEvent::class)]
#[CoversClass(User::class)]
final class RegisterUserCommandTest extends KernelTestCase
{
    private RegisterUserServiceContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var RegisterUserServiceContext $context */
        $context = self::getContainer()->get(RegisterUserServiceContext::class);

        $this->context = $context;
    }

    public function testUserIsRegisteredSuccessfully(): void
    {
        $command = new RegisterUserCommand('878a983e-1b1c-472c-8da0-97dc5e4bfb8f', 'test@email.com', 'jG\Qc_g7;%zE85');

        $command->run($this->context);

        $this->context->entityManager->clear();

        $user = $this->context->userRepository->find('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');

        self::assertNotNull($user);
    }
}
