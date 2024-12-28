<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model;

use App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\Email\Email;
use App\EmployeePortal\Authentication\Domain\User\Password\Password;
use App\EmployeePortal\Authentication\Domain\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use EmployeePortal\Authentication\Domain\User\Support\Event\UserEvent;
use EmployeePortal\Authentication\Domain\User\Support\Event\UserEventVisitor;
use EmployeePortal\Authentication\Domain\User\Support\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
final readonly class UserRegisteredEvent implements UserEvent
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
    public function execute(UserRepository $userRepository): void
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
