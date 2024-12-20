<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Action\Register;

use App\EmployeePortal\Authentication\Domain\User\Email\Email;
use App\EmployeePortal\Authentication\Domain\User\Password\Password;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\User\Event\UserEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\Domain\User\User\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Note that if client code already has UserRegisteredEvent, it would definitely mean that:
 * - email was unique at the moment process() method was called
 * - this event has already been applied to user entity
 * - the business logic has successfully completed
 */
#[ORM\Entity]
final readonly class UserRegistrationEvent implements UserEvent
{
    public function __construct(
        /** Event ID */
        private Uuid $id,
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        private Email $email,
        private Password $password,
        private CarbonImmutable $timestamp,
    ) {
    }

    /**
     * The key business logic of user registration should be placed in process() method of the domain event.
     * It is completely responsible for implementation of all the necessary checks that business scenario defines.
     */
    public function run(UserRepository $userRepository): void
    {
        if (!$userRepository->isEmailFree($this->email)) {
            throw new EmailAlreadyTakenException($this->email);
        }

        $this->user->register($this);
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserRegisteredEvent($this, $data);
    }
}
