<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Stories\Register;

use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Password\Password;
use App\EmployeePortal\Authentication\User\Stories\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\User\Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'user_registered_events')]
class UserRegisteredEvent extends UserEvent
{
    protected const string TYPE = 'userRegistered';

    public function __construct(
        protected Uuid $id,
        protected User $user,
        #[ORM\Embedded(columnPrefix: false)]
        private readonly Email $email,
        #[ORM\Embedded]
        private readonly Password $password,
        protected CarbonImmutable $timestamp,
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

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserRegisteredEvent($this, $data);
    }
}
