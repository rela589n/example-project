<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Note that if client code already has UserRegisteredEvent, it would definitely mean that:
 * - email was unique at the moment process() method was called
 * - this event has already been applied to user entity
 * - the business logic has successfully completed
 */
#[ORM\Entity]
final readonly class UserRegisteredEvent implements UserEvent
{
    private function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        private Email $email,
        private Password $password,
        private CarbonImmutable $timestamp,
    ) {
    }

    public static function process(Uuid $id, Email $email, Password $password, DateTimeImmutable $timestamp): self
    {
        $event = new self(new User($id), $email, $password, CarbonImmutable::instance($timestamp));

        $event->apply();

        return $event;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }

    private function apply(): void
    {
        $this->user->register($this);
    }
}
