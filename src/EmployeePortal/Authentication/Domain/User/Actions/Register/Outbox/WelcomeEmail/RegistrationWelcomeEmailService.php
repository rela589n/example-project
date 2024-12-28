<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Actions\Register\Outbox\WelcomeEmail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler('consumer.bus')]
final readonly class RegistrationWelcomeEmailService
{
    public function __construct(
        public Environment $twig,
        public MailerInterface $mailer,
    ) {
    }

    public function __invoke(RegistrationWelcomeEmail $email): void
    {
        $email->send($this);
    }
}
