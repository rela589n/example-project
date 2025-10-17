<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Apply;

final class Item
{
    public function __construct(
        private(set) int $id,
        private(set) int $price,
        public ?int $price_with_discount = null,
    ) {
    }

    public function getDiscountAmount(int $discount, int $totalPrice)
    {
        // price, total price
        // 400,   1000
        $percent = $this->price / $totalPrice;


    }

    public function applyDiscount(int $discount): self
    {
        $self = clone $this;
        $self->price_with_discount = $this->price - $discount;
        return $self;
    }

    public function withPriceWithDiscount(int $price_with_discount): self
    {
        $self = clone $this;
        $self->price_with_discount = $price_with_discount;
        return $self;
    }

    public static function fromArray(array $item): Item
    {
        return new self(
            $item['id'],
            $item['price'],
        );
    }
}
