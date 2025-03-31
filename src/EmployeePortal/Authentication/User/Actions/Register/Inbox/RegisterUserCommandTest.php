<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Register\Inbox;

use App\EmployeePortal\Authentication\User\Actions\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\Actions\Register\UserRegisteredEventTest;
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
    /** @throws Exception */
    public function testUserIsRegisteredSuccessfully(): void
    {
        $command = new RegisterUserCommand('878a983e-1b1c-472c-8da0-97dc5e4bfb8f', 'test@email.com', 'jG\Qc_g7;%zE85');

        $service = $this->getService();

        $command->execute($service);

        $service->entityManager->clear();

        $user = $service->userRepository->find('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');

        self::assertNotNull($user);
    }

    private function getService(): RegisterUserService
    {
        /** @var RegisterUserService $service */
        $service = self::getContainer()->get(RegisterUserService::class);

        return $service;
    }
}
