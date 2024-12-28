<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\Command;

use App\EmployeePortal\Authentication\User\PasswordReset\Repository\PasswordResetRequestRepository;
use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ResetUserPasswordService
{
    public function __construct(
        public UserRepository $userRepository,
        public PasswordResetRequestRepository $passwordResetRequestRepository,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
        public ClockInterface $clock,
    ) {
    }

    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $command->process($this);
    }
}
