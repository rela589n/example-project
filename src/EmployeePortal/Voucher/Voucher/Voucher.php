<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher;

use App\EmployeePortal\Voucher\Voucher\Features\Apply\Item;
use App\EmployeePortal\Voucher\Voucher\Features\Create\VoucherCreatedEvent;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'service_vouchers')]
class Voucher
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    #[ORM\Column(type: 'integer')]
    private int $discount;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $updatedAt;

    public function __construct(VoucherCreatedEvent $event)
    {
        $this->id = $event->getId();
        $this->discount = $event->getDiscount();
        $this->createdAt = $event->getTimestamp();
        $this->updatedAt = $event->getTimestamp();
    }

    /** @param Item[] $items */
    public function prorate(array $items): array
    {
        $prices = array_map(static fn (Item $item) => $item->price, $items);
        $totalSum = array_sum($prices);

        if ($this->discount >= $totalSum) {
            return array_map(static fn (Item $item) => $item->withPriceWithDiscount(0), $items);
        }
        // [400, 600], d 100 => d 100 / t 1000
        //  -40, -60
        // [360, 540],

        // [1, 999], 100
        // [1, 900]

        $mainDiscount = 0;

        $results = [];

        foreach ($items as $item) {
            $v = $item->price * $this->discount; // 400 * 100

            $discount = intdiv($v, $totalSum); //  400 * 100 / 1000 => 40

            $mainDiscount += $discount;

            $results[] = $item->applyDiscount($discount);
        }

        $remnant = $this->discount - $mainDiscount;

        while ($remnant > 0) {
            foreach ($results as $result) {
                if ($remnant === 0) {
                    break;
                }

                if ($result->price_with_discount === 0) {
                    continue;
                }

                --$result->price_with_discount;
                --$remnant;
            }
        }

        return $results;

        // [123, 123] 123
        // [62, 61]
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->updatedAt;
    }
}
