<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Actions\Register\Outbox\WelcomeEmail;

use App\EmployeePortal\Authentication\Domain\User\Email\Email;

final readonly class RegistrationWelcomeEmail
{
    public function __construct(
        private Email $email,
    ) {
    }

    public function send(RegistrationWelcomeEmailService $service)
    {
        $service->mailer->send();
    }
}
