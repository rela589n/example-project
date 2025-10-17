<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\Voucher\Features\Apply;

use App\EmployeePortal\Service\Voucher\Voucher;
use Symfony\Component\Uid\Uuid;

final readonly class ApplyVoucherCommand
{
    private(set) Uuid $id;

    private(set) array $itemsWithDiscount;

    public function __construct(
        private string $code,
        /** @var Item[] */
        private array $items,
    ) {
        $this->id = Uuid::fromBase58($this->code);
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['code'],
            array_map(fn(array $item) => Item::fromArray($item), $array['items']),
        );
    }

    public function process(ApplyVoucherService $service): void
    {
        /** @var Voucher $voucher */
        $voucher = $service->entityManager->find(Voucher::class, $this->id);

        $this->itemsWithDiscount = $voucher->prorate($this->items);
    }
}
