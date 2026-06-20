<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\_Features\Reset\Port;

use App\EmployeePortal\Authentication\User\_Support\Repository\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\PasswordReset\_Features\Reset\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\User\PasswordReset\_Features\Reset\UserResetPasswordEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\PasswordReset\Repository\Exception\PasswordResetRequestNotFoundException;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use PhPhD\ExceptionalMatcher\Rule\Object\Property\Catch_;
use PhPhD\ExceptionalMatcher\Rule\Object\Property\Match\Condition\Value\ExceptionValueMatchCondition;
use PhPhD\ExceptionalMatcher\Rule\Object\Try_;
use Symfony\Component\Uid\Uuid;

use function Amp\async;
use function Amp\Future\awaitAnyN;

#[Try_]
final readonly class ResetUserPasswordCommand
{
    #[Catch_(UserNotFoundException::class, condition: ExceptionValueMatchCondition::class)]
    private string $userId;

    #[Catch_(PasswordResetRequestNotFoundException::class, condition: ExceptionValueMatchCondition::class)]
    #[Catch_(ExpiredPasswordResetRequestException::class, condition: ExceptionValueMatchCondition::class)]
    private string $passwordResetRequestId;

    private User $user;

    private PasswordResetRequest $passwordResetRequest;

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

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function getPasswordResetRequestId(): Uuid
    {
        return Uuid::fromString($this->passwordResetRequestId);
    }

    private function createEvent(ResetUserPasswordService $service): UserResetPasswordEvent
    {
        /**
         * One more thing about awaitAnyN() is that it actually allows us to benefit from async i/o
         * In case if doctrine will add support for it in the future, the code would become faster
         * without it being changed in any way.
         */
        [
            fn () => $this->user = $this->findUser($service),
            fn () => $this->passwordResetRequest = $this->findPasswordResetRequest($service),
        ]
            |> (static fn (array $closures) => array_map(async(...), $closures))
            |> (static fn (array $futures) => awaitAnyN(2, $futures));

        //  [$user, $passwordResetRequest] = [
        //      fn () => $this->findUser($service),
        //      fn () => $this->findPasswordResetRequest($service),
        //  ] |> concurrent(...) |> settle(...);

        return new UserResetPasswordEvent(Uuid::v7(), $this->user, $this->passwordResetRequest, CarbonImmutable::instance($service->clock->now()));
    }

    private function findUser(ResetUserPasswordService $service): User
    {
        return $service->userRepository->findById($this->getUserId());
    }

    private function findPasswordResetRequest(ResetUserPasswordService $service): PasswordResetRequest
    {
        return $service->passwordResetRequestRepository->findById($this->getPasswordResetRequestId());
    }
}
