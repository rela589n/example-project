<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account;

use App\EmployeePortal\Accounting\Account\Features\Create\AccountCreatedEvent;
use App\Support\Partitioning\Entity\PartitionedEntityInterface;
use App\Support\Partitioning\Entity\PartitionId;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'accounting_accounts')]
class Account implements PartitionedEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

    #[ORM\Column]
    private int $number;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $updatedAt;

    public function create(AccountCreatedEvent $event): void
    {
        $this->id = $event->getId();
        $this->userId = $event->getUserId();
        $this->number = $event->getNumber();
        $this->createdAt = $event->getTimestamp();
        $this->updatedAt = $event->getTimestamp();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getPartitionId(): PartitionId
    {
        return new PartitionId($this->userId->toRfc4122(), $this->userId->toBase58());
    }
}
