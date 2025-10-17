<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\Voucher;

use App\EmployeePortal\Service\Voucher\Features\Create\VoucherCreatedEvent;
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
