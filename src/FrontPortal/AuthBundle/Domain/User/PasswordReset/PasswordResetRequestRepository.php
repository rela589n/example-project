<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\PasswordReset;

use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Exception\PasswordResetRequestNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class PasswordResetRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetRequest::class);
    }

    public function findById(Uuid $id): User
    {
        return $this->find($id) ?? throw new PasswordResetRequestNotFoundException(id: $id);
    }
}
