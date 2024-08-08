<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;

    private Email $email;

    private Password $password;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function processRegisteredEvent(UserRegisteredEvent $event): void
    {
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
    }
}
