<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register;

use App\FrontPortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ObjectRepository;
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
    ) {
    }

    public static function process(Email $email, Password $password, ObjectRepository $userRepository): self
    {
        if (!self::isEmailFree($email, $userRepository)) {
            throw new EmailAlreadyTakenException($email);
        }

        $event = new self(new User(Uuid::v7()), $email, $password);

        $event->apply();

        return $event;
    }

    private static function isEmailFree(Email $email, ObjectRepository $userRepository): bool
    {
        $existingUser = $userRepository->findOneBy(['email.email' => $email->getEmail()]);

        return null === $existingUser;
    }

    public function apply(): void
    {
        $this->user->register($this);
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
}
