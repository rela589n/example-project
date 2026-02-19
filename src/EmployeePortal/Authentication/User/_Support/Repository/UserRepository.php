<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\_Support\Repository;

use App\EmployeePortal\Authentication\User\_Support\Repository\Exception\UserNotFoundException;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/** @extends ServiceEntityRepository<User> */
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
        return $this->findOneBy(['email.email' => $email->toString()]) ?? throw new UserNotFoundException(email: $email);
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
