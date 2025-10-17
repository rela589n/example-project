<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Apply\Port;

use App\EmployeePortal\Voucher\Voucher\Features\Apply\Item;
use App\EmployeePortal\Voucher\Voucher\Voucher;
use Symfony\Component\Uid\Uuid;

final readonly class ApplyVoucherCommand
{
    private(set) Uuid $id;

    private(set) array $itemsWithDiscount;

    /** @param array<array-key,Item> $items */
    public function __construct(
        private string $code,
        private array $items,
    ) {
        $this->id = Uuid::fromBase58($this->code);
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['code'],
            array_map(static fn (array $item) => Item::fromArray($item), $array['items']),
        );
    }

    public function process(ApplyVoucherService $service): void
    {
        /** @var Voucher $voucher */
        $voucher = $service->entityManager->find(Voucher::class, $this->id);

        $this->itemsWithDiscount = $voucher->prorate($this->items);
    }
}
