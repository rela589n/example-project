<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\GetList;

use App\EmployeePortal\Accounting\Account\Account;
use Carbon\CarbonImmutable;
use JsonSerializable;
use Symfony\Component\Uid\Uuid;

final readonly class AccountDto implements JsonSerializable
{
    private function __construct(
        private Uuid $id,
        private int $number,
        private CarbonImmutable $createdAt,
    ) {
    }

    public static function fromEntity(Account $account): self
    {
        return new self(
            $account->getId(),
            $account->getNumber(),
            $account->getCreatedAt(),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'number' => $this->number,
            'createdAt' => $this->createdAt->toIso8601String(),
        ];
    }
}
