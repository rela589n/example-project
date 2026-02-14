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
    private(set) int $discount;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $updatedAt;

    public function __construct(VoucherCreatedEvent $event)
    {
        $this->id = $event->id;
        $this->discount = $event->discount;
        $this->createdAt = $event->timestamp;
        $this->updatedAt = $event->timestamp;
    }

    /**
     * 100 for [400, 600]
     *         [-40, -60]
     *         [360, 540],
     * 100 for 1000
     * 0.1 for discount
     * 0.9 for each
     *
     * 2 for [  1,   2,     3]
     *       [ 0.66, 1.33,  2]
     *       [ 1,    2,      2]
     * 2 for 6
     * 0.3333333 for discount
     * 0.6 for each
     *
     *
     * 100 for [1, 999]
     *         [1, 899]
     * 0.9 for each
     *
     * @param list<Item> $items
     *
     * @return list<Item>
     */
    public function prorate(array $items): array
    {
        $prices = array_map(static fn (Item $item) => $item->price, $items);
        $totalPrice = array_sum($prices);

        if ($this->discount >= $totalPrice) {
            return array_map(static fn (Item $item) => $item->withPriceWithDiscount(0), $items);
        }

        return $this->initialProrateSolution($items, $totalPrice);
    }

    /**
     * @param array<array-key,Item> $items
     *
     * [123, 123] 123
     * [62, 61]
     *
     * @return list<Item>
     */
    private function initialProrateSolution(array $items, int $totalSum): array
    {
        $mainDiscount = 0;

        $results = [];

        foreach ($items as $item) {
            $v = $item->price * $this->discount; // 400 * 100

            $discount = intdiv($v, $totalSum); //  400 * 100 / 1000 => 40

            $mainDiscount += $discount;

            $results[] = $item->subtractDiscount($discount);
        }

        $remnant = $this->discount - $mainDiscount;

        while ($remnant > 0) {
            foreach ($results as $result) {
                if ($remnant === 0) {
                    break;
                }

                if (!$result->price_with_discount) {
                    continue;
                }

                --$result->price_with_discount;
                --$remnant;
            }
        }

        return $results;
    }
}
