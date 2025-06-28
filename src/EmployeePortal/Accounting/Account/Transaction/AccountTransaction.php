<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Transaction;

use App\EmployeePortal\Accounting\Account\Account;
use App\EmployeePortal\Accounting\Account\ScopedId;
use App\Support\Partitioning\Entity\PartitionedEntityInterface;
use App\Support\Partitioning\Entity\PartitionId;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'accounting_account_transactions')]
class AccountTransaction implements PartitionedEntityInterface
{
    #[ORM\Embedded(columnPrefix: false)]
    private ScopedId $id;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    private Account $account;

    #[ORM\Column]
    private int $amount;

    #[ORM\Column(length: 255)]
    private string $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;

    public function __construct(
        Uuid $id,
        Account $account,
        int $amount,
        string $description,
        CarbonImmutable $createdAt
    ) {
        $this->id = new ScopedId($id, $account->getUserId());
        $this->account = $account;
        $this->amount = $amount;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }


    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getPartitionId(): PartitionId
    {
        return $this->account->getPartitionId();
    }
}
