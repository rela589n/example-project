<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\Model\Event;

use App\FrontPortal\AuthBundle\Domain\Model\User;
use App\FrontPortal\AuthBundle\Domain\Model\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\Model\ValueObject\Password;
use App\Support\Functional\Operation\ThunkList;
use Closure;
use Exception;

final readonly class UserRegisteredEvent implements UserEvent
{
    public function __construct(
        private User $user,
        private Email $email,
        private Password $password,
    ) {
    }

    /**
     * @param Closure(): User $userFn
     * @param Closure(): Email $emailFn
     * @param Closure(): Password $passwordFn
     */
    public static function unwrap(Closure $userFn, Closure $emailFn, Closure $passwordFn): self
    {
        [$user, $email, $password] = ThunkList::of($userFn, $emailFn, $passwordFn)->__invoke();

        // In order to collect all validation errors at once, it's necessary to call these closures
        // one by one, collecting thrown exceptions into the list.

        $exceptions = [];

        $params = array_map(static function (Closure $closure) use (&$exceptions) {
            try {
                return $closure();
            } catch (Exception $e) {
                $exceptions[] = $e;

                return null;
            }
        }, [$userFn, $emailFn, $passwordFn]);

        if ([] !== $exceptions) {
            throw new CompositeException($exceptions);
        }

        return new self($user, $email, $password);
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
