<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Stories\Register\Outbox\WelcomeEmail;

use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

//#[AsMessageHandler('consumer.bus')]
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
