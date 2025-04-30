<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Port;

use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEventTest;
use App\EmployeePortal\Authentication\User\User;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Handlers are full of infrastructure code, - therefore they are hard to unit test.
 * This is just an example of what is better not to do.
 * @see UserRegisteredEventTest as a better alternative
 */
#[CoversClass(UserRegisteredEvent::class)]
#[CoversClass(User::class)]
final class RegisterUserCommandTest extends KernelTestCase
{
    private RegisterUserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var RegisterUserService $service */
        $service = self::getContainer()->get(RegisterUserService::class);

        $this->service = $service;
    }

    /** @throws Exception */
    public function testUserIsRegisteredSuccessfully(): void
    {
        $command = new RegisterUserCommand('878a983e-1b1c-472c-8da0-97dc5e4bfb8f', 'test@email.com', 'jG\Qc_g7;%zE85');

        $command->process($this->service);

        $this->service->entityManager->clear();

        $user = $this->service->userRepository->find('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');

        self::assertNotNull($user);

        $userRegisteredEvent = $user->getEvents()->get('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');

        self::assertInstanceOf(UserRegisteredEvent::class, $userRegisteredEvent);
    }
}
