<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Outbox\WelcomeEmail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler('consumer.bus')]
final readonly class SendWelcomeEmailService
{
    public function __construct(
        public Environment $twig,
        public MailerInterface $mailer,
    ) {
    }

    public function __invoke(SendWelcomeEmailCommand $command): void
    {
        $command->execute($this);
    }
}
