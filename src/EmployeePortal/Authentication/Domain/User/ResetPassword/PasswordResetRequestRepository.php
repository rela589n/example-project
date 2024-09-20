<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\ResetPassword;

use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Exception\PasswordResetRequestNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class PasswordResetRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetRequest::class);
    }

    public function findById(Uuid $id): PasswordResetRequest
    {
        return $this->find($id) ?? throw new PasswordResetRequestNotFoundException(id: $id);
    }
}
