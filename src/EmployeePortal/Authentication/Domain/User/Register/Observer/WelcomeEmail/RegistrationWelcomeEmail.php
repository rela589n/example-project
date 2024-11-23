<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Observer\WelcomeEmail;

use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;

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
