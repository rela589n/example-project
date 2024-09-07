<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Reset\Handler;

use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\PasswordResetRequest;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\PasswordResetRequestRepository;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Reset\UserPasswordResetEvent;
use App\EmployeePortal\AuthBundle\Domain\User\User;
use App\EmployeePortal\AuthBundle\Domain\User\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ResetUserPasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetRequestRepository $passwordResetRequestRepository,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $resetPassword = UserPasswordResetEvent::process($this->clock);

        /**
         * One more thing about awaitAnyN() is that it actually allows us to benefit from async i/o
         * In case if doctrine will add support for it in the future, the code would become faster
         * without being changed in any way.
         *
         * @var User $user
         * @var PasswordResetRequest $passwordResetRequest
         */
        [$user, $passwordResetRequest] = awaitAnyN(2, [
            async(fn (): User => $this->findUser($command)),
            async(fn (): PasswordResetRequest => $this->findPasswordResetRequest($command)),
        ]);

        $event = $resetPassword($user, $passwordResetRequest);

        $this->eventBus->dispatch($event);
    }

    private function findUser(ResetUserPasswordCommand $command): User
    {
        return $this->userRepository->findById($command->getUserId());
    }

    private function findPasswordResetRequest(ResetUserPasswordCommand $command): PasswordResetRequest
    {
        return $this->passwordResetRequestRepository->findById($command->getPasswordResetRequestId());
    }
}