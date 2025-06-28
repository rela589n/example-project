<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\Create;

use App\EmployeePortal\Accounting\Account\Account;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class AccountCreatedEvent
{
    public function __construct(
        private Uuid $id,
        private Account $account,
        private Uuid $userId,
        private int $number,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(): void
    {
        $this->account->create($this);
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
