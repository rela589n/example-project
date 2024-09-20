<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register;

use App\EmployeePortal\Authentication\Domain\User\Event\UserEvent;
use App\EmployeePortal\Authentication\Domain\User\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\UserRepository;
use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;
use App\EmployeePortal\Authentication\Domain\ValueObject\Password\Password;
use Carbon\CarbonImmutable;
use Closure;
use Doctrine\ORM\Mapping as ORM;
use Psr\Clock\ClockInterface;
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
        /** Event ID */
        private Uuid $id,
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        private Email $email,
        private Password $password,
        private CarbonImmutable $timestamp,
    ) {
    }

    /** @return Closure(Uuid $id, Email $email, Password $password): self  */
    public static function process(ClockInterface $clock, UserRepository $userRepository): Closure
    {
        return static function (Uuid $id, Email $email, Password $password) use ($clock, $userRepository): self {
            $event = new self($id, new User($id), $email, $password, CarbonImmutable::instance($clock->now()));

            $event->run($userRepository);

            return $event;
        };
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

    /**
     * The key business logic of user registration should be placed in run() method of the event.
     * It is completely responsible for implementation of all the necessary checks that business scenario defines.
     */
    private function run(UserRepository $userRepository): void
    {
        if (!$userRepository->isEmailFree($this->email)) {
            throw new EmailAlreadyTakenException($this->email);
        }

        $this->user->register($this);
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserRegisteredEvent($this, $data);
    }
}
