<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\Create;

use App\EmployeePortal\Accounting\Account\Account;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class AccountCreatedEvent
{
    private Account $account;

    public function __construct(
        private Uuid $id,
        private Uuid $userId,
        private int $number,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Account
    {
        return $this->account = new Account($this);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }
}
