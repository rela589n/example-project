<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Apply\Port;

use App\EmployeePortal\Voucher\Voucher\Features\Apply\Item;
use App\EmployeePortal\Voucher\Voucher\Voucher;
use Symfony\Component\Uid\Uuid;

final readonly class ApplyVoucherCommand
{
    private(set) Uuid $id;

    /** @var array<array-key,Item> */
    private(set) array $itemsWithDiscount;

    /** @param array<array-key,Item> $items */
    public function __construct(
        private string $code,
        private array $items,
    ) {
        $this->id = Uuid::fromBase58($this->code);
    }

    public function process(ApplyVoucherService $service): void
    {
        /** @var Voucher $voucher */
        $voucher = $service->entityManager->find(Voucher::class, $this->id);

        $this->itemsWithDiscount = $voucher->prorate($this->items);
    }
}
