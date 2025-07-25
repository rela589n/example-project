<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Support;

use App\Support\Partitioning\Entity\PartitionId;
use App\Support\Partitioning\Resolve\PartitionIdResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Uid\Uuid;

final readonly class AppPartitionIdResolver implements PartitionIdResolver
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function resolve(): ?PartitionId
    {
        $userIdentifier = $this->tokenStorage->getToken()?->getUserIdentifier();

        if (null === $userIdentifier) {
            return null;
        }

        try {
            $id = Uuid::fromString($userIdentifier);
        } catch (\InvalidArgumentException) {
            return null;
        }

        return new PartitionId($id->toRfc4122(), $id->toBase58());
    }
}
