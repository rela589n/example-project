<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Get\Port;

use App\EmployeePortal\Entity\Entity\Entity;
use Symfony\Component\Uid\Uuid;

final class GetEntityQuery
{
    /** @var array<string,mixed> */
    private array $result;

    public function __construct(
        private(set) Uuid $id,
    ) {
    }

    public function process(GetEntityService $service): void
    {
        $entity = $service->entityCollection->get($this->id);

        $this->result = [
            'id' => $entity->id->toRfc4122(),
            'created_at' => $entity->createdAt->format(\DateTimeInterface::ATOM),
            'updated_at' => $entity->updatedAt->format(\DateTimeInterface::ATOM),
        ];
    }

    /** @return array<string,mixed> */
    public function getResult(): array
    {
        return $this->result;
    }
}
