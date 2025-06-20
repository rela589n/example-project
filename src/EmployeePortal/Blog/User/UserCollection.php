<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\User;

use App\EmployeePortal\Blog\Support\EntityNotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Symfony\Component\Uid\Uuid;

final readonly class UserCollection
{
    public function __construct(
        /** @var Selectable<User>&Collection<User> */
        private Collection $collection,
    ) {
    }

    public function get(Uuid $id): User
    {
        return $this->collection->get($id->toRfc4122())
            ?? throw new EntityNotFoundException($id);
    }
}
