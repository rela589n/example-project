<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Reset\Handler;

use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\PasswordResetRequestRepository;
use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Reset\UserPasswordResetEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private PasswordResetRequestRepository $passwordResetRequestRepository,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
        private ClockInterface $clock,
    ) {
    }

    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $event = $this->processEvent($command);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $this->eventBus->dispatch($event);
    }

    private function processEvent(ResetUserPasswordCommand $command): UserPasswordResetEvent
    {
        /**
         * @var User $user
         * @var PasswordResetRequestRepository $passwordResetRequest
         */
        [$user, $passwordResetRequest] = awaitAnyN(2, [
            async(fn (): User => $this->findUser($command)),
            async(fn (): PasswordResetRequestRepository => $this->findPasswordResetRequest($command)),
        ]);

        return UserPasswordResetEvent::process($this->clock)($user, $passwordResetRequest);
    }

    private function findUser(ResetUserPasswordCommand $command): User
    {
        return $this->userRepository->findById($command->getUserId());
    }

    private function findPasswordResetRequest(ResetUserPasswordCommand $command): PasswordResetRequestRepository
    {
        return $this->passwordResetRequestRepository->findById($command->getPasswordResetRequestId());
    }
}
