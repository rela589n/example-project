<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'accounting_account_transactions')]
class AccountTransaction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $userId;

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
        Uuid $userId,
        int $amount,
        string $description,
        CarbonImmutable $createdAt
    ) {
        $this->id = $id;
        $this->account = $account;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
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
}
