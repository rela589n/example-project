<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\Login;

use App\EmployeePortal\AuthBundle\Domain\User\Event\UserEvent;
use App\EmployeePortal\AuthBundle\Domain\User\Event\UserEventVisitor;
use App\EmployeePortal\AuthBundle\Domain\User\User;
use Carbon\CarbonImmutable;
use Closure;
use Doctrine\ORM\Mapping as ORM;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[ORM\Entity]
final readonly class UserLoggedInEvent implements UserEvent
{
    private function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        private CarbonImmutable $timestamp,
    ) {
    }

    /** @return Closure(User $user, string $plainPassword): self */
    public static function process(ClockInterface $clock, PasswordHasherInterface $passwordHasher): Closure
    {
        return static function (User $user, string $plainPassword) use ($clock, $passwordHasher) {
            $event = new self($user, CarbonImmutable::instance($clock->now()));

            $event->run($passwordHasher, $plainPassword);

            return $event;
        };
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }

    private function run(PasswordHasherInterface $passwordHasher, string $plainPassword): void
    {
        $this->user->getPassword()->verify($passwordHasher, $plainPassword);

        $this->user->logIn($this);
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserLoggedInEvent($this, $data);
    }
}
