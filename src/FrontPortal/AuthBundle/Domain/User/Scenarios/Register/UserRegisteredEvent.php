<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register;

use Amp\Future;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @param User $user
     * @param Future<Email> $email
     * @param Future<Password> $password
     */
    public static function of(User $user, Future $email, Future $password): self
    {
        $futures = [$email, $password];

        $results = Future\awaitAnyN(count($futures), $futures);

        return new self($user, ...$results);
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

    public function process(): void
    {
        $this->user->processRegisteredEvent($this);
    }
}
