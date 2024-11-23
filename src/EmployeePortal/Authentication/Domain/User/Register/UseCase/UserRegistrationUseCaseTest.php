<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\UseCase;

use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegistrationEvent;
use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegistrationEventTest;
use App\EmployeePortal\Authentication\Domain\User\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Handlers are full of infrastructure code, - therefore they are hard to unit test.
 * This is just an example of what is better not to do.
 * @see UserRegistrationEventTest as a better alternative
 */
#[CoversClass(UserRegistrationEvent::class)]
#[CoversClass(User::class)]
final class UserRegistrationUseCaseTest extends KernelTestCase
{
    private UserRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRegistrationService $userRegistrationService */
        $userRegistrationService = self::getContainer()->get(UserRegistrationService::class);

        $this->service = $userRegistrationService;
    }

    public function testUserIsRegisteredSuccessfully(): void
    {
        $useCase = new UserRegistrationUseCase('878a983e-1b1c-472c-8da0-97dc5e4bfb8f', 'test@email.com', 'jG\Qc_g7;%zE85');

        $this->service->__invoke($useCase);

        $this->service->entityManager->clear();

        $user = $this->service->userRepository->find('878a983e-1b1c-472c-8da0-97dc5e4bfb8f');
        self::assertNotNull($user);
    }
}
