<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\_Features\Create;

use App\EmployeePortal\Voucher\Voucher\Voucher;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class VoucherCreatedEvent
{
    private Voucher $voucher;

    public function __construct(
        private(set) Uuid $id,
        private(set) int $discount,
        private(set) CarbonImmutable $timestamp,
    ) {
    }

    public function process(): Voucher
    {
        return $this->voucher = new Voucher($this);
    }

    public function getVoucher(): Voucher
    {
        return $this->voucher;
    }
}
