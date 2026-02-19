<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Login;

use App\EmployeePortal\Authentication\User\_Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\_Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'user_logged_in_events')]
final class UserLoggedInEvent extends UserEvent
{
    protected const string TYPE = 'loggedIn';

    public function __construct(
        protected Uuid $id,
        protected User $user,
        protected CarbonImmutable $timestamp,
    ) {
    }

    public function process(string $plainPassword, PasswordHasherInterface $passwordHasher): void
    {
        $this->user->getPassword()->verify($plainPassword, $passwordHasher);

        $this->user->login($this);
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserLoggedInEvent($this, $data);
    }
}
