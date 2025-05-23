<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Outbox\WelcomeEmail;

use App\EmployeePortal\Authentication\User\Email\Email;


final readonly class SendWelcomeEmailCommand
{
    public function __construct(
        private Email $email,
    ) {
    }

    public function execute(SendWelcomeEmailService $service): void
    {
        $mail = new \Symfony\Component\Mime\Email();

        $mail->from('no-reply@example.com')
            ->to($this->email->toString())
            ->subject('Welcome to Example Project')
            ->text('Welcome to the Example Project! We are glad to see you.')
            ->html('<p>Welcome to the Example Project! We are glad to see you.</p>');

        $service->mailer->send($mail);
    }
}
