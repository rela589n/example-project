<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Login\Outbox\WelcomeEmail;


use Symfony\Component\Uid\Uuid;

final readonly class SendWelcomeEmailCommand
{
    public function __construct(
        private Uuid $userId,
    ) {
    }

    public function execute()
    {
        // sending the email
    }
}
