<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Repository;

use App\EmployeePortal\Authentication\Domain\User\Email\Email;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Exception\UserNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findById(Uuid $id): User
    {
        return $this->find($id) ?? throw new UserNotFoundException(id: $id);
    }

    public function findByEmail(Email $email): User
    {
        return $this->findOneBy(['email.email' => $email]) ?? throw new UserNotFoundException(email: $email);
    }

    public function isEmailFree(Email $email): bool
    {
        try {
            $this->findByEmail($email);
        } catch (UserNotFoundException) {
            return true;
        }

        return false;
    }
}
