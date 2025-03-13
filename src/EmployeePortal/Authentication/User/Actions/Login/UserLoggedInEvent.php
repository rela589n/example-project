<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Login;

use App\EmployeePortal\Authentication\User\Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
final readonly class UserLoggedInEvent implements UserEvent
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private Uuid $id,
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        #[ORM\Column(type: 'carbon_immutable')]
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(PasswordHasherInterface $passwordHasher, string $plainPassword): void
    {
        $this->user->getPassword()->verify($passwordHasher, $plainPassword);

        $this->user->login($this);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserLoggedInEvent($this, $data);
    }
}
