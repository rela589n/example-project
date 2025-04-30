<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Stories\Reset\Inbox;

use PhPhD\ExceptionalValidation\Rule\Object\Property\Capture\Condition\Value\ExceptionValueMatchCondition;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\PasswordReset\Repository\Exception\PasswordResetRequestNotFoundException;
use App\EmployeePortal\Authentication\User\PasswordReset\Stories\Reset\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\User\PasswordReset\Stories\Reset\UserPasswordResetEvent;
use App\EmployeePortal\Authentication\User\Support\Repository\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use Symfony\Component\Uid\Uuid;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[ExceptionalValidation]
final readonly class ResetUserPasswordCommand
{
    #[Capture(UserNotFoundException::class, condition: ExceptionValueMatchCondition::class)]
    private string $userId;

    #[Capture(PasswordResetRequestNotFoundException::class, condition: ExceptionValueMatchCondition::class)]
    #[Capture(ExpiredPasswordResetRequestException::class, condition: ExceptionValueMatchCondition::class)]
    private string $passwordResetRequestId;

    public function __construct(
        string $userId,
        string $passwordResetRequestId,
    ) {
        $this->userId = $userId;
        $this->passwordResetRequestId = $passwordResetRequestId;
    }

    public function process(ResetUserPasswordService $service): void
    {
        $event = $this->createEvent($service);

        $event->process();

        $service->eventBus->dispatch($event);
    }

    private function createEvent(ResetUserPasswordService $service): UserPasswordResetEvent
    {
        /**
         * One more thing about awaitAnyN() is that it actually allows us to benefit from async i/o
         * In case if doctrine will add support for it in the future, the code would become faster
         * without it being changed in any way.
         *
         * @var User $user
         * @var PasswordResetRequest $passwordResetRequest
         */
        [$user, $passwordResetRequest] = awaitAnyN(2, [
            async($this->getUser(...), $service),
            async($this->getPasswordResetRequest(...), $service),
        ]);

        return new UserPasswordResetEvent(Uuid::v7(), $user, $passwordResetRequest, CarbonImmutable::instance($service->clock->now()));
    }

    private function getUser(ResetUserPasswordService $service): User
    {
        return $service->userRepository->findById($this->getUserId());
    }

    private function getPasswordResetRequest(ResetUserPasswordService $service): PasswordResetRequest
    {
        return $service->passwordResetRequestRepository->findById($this->getPasswordResetRequestId());
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function getPasswordResetRequestId(): Uuid
    {
        return Uuid::fromString($this->passwordResetRequestId);
    }
}
