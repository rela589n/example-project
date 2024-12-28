<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\Command;

use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\UserPasswordResetEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\PasswordReset\Repository\Exception\PasswordResetRequestNotFoundException;
use App\EmployeePortal\Authentication\User\Support\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use PhPhD\ExceptionalValidation;
use PhPhD\ExceptionalValidation\Capture;
use PhPhD\ExceptionalValidation\Model\Condition\ValueExceptionMatchCondition;
use Symfony\Component\Uid\Uuid;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[ExceptionalValidation]
final readonly class ResetUserPasswordCommand
{
    #[Capture(UserNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    private string $userId;

    #[Capture(PasswordResetRequestNotFoundException::class, condition: ValueExceptionMatchCondition::class)]
    #[Capture(ExpiredPasswordResetRequestException::class, condition: ValueExceptionMatchCondition::class)]
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
        /**
         * One more thing about awaitAnyN() is that it actually allows us to benefit from async i/o
         * In case if doctrine will add support for it in the future, the code would become faster
         * without being changed in any way.
         *
         * @var User $user
         * @var PasswordResetRequest $passwordResetRequest
         */
        [$user, $passwordResetRequest] = awaitAnyN(2, [
            async(fn (): User => $this->findUser($service)),
            async(fn (): PasswordResetRequest => $this->findPasswordResetRequest($service)),
        ]);

        $event = new UserPasswordResetEvent($user, $passwordResetRequest, CarbonImmutable::instance($service->clock->now()));

        $event->process();

        $service->eventBus->dispatch($event);
    }

    private function findUser(ResetUserPasswordService $service): User
    {
        return $service->userRepository->findById($this->getUserId());
    }

    private function findPasswordResetRequest(ResetUserPasswordService $service): PasswordResetRequest
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
