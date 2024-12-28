<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Actions\Register\Service;

use App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model\UserRegisteredEvent;
use App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model\UserRegisteredEventTest;
use App\EmployeePortal\Authentication\Domain\User\User;
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

        $command->execute($this->context);

        $this->context->entityManager->clear();

        $user = $this->context->userRepository->find('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');

        self::assertNotNull($user);
    }
}
