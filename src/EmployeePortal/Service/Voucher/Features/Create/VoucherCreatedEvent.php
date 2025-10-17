<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\Voucher\Features\Create;

use App\EmployeePortal\Service\Voucher\Voucher;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class VoucherCreatedEvent
{
    private Voucher $voucher;

    public function __construct(
        private Uuid $id,
        private int $discount,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Voucher
    {
        return $this->voucher = new Voucher($this);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }
}
