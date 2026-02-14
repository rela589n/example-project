<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Create\Port;

use App\EmployeePortal\Voucher\Voucher\Features\Create\VoucherCreatedEvent;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

final readonly class CreateVoucherCommand
{
    private(set) Uuid $id;

    public function __construct(
        private int $discount,
        #[Ignore] // @phpstan-ignore attribute.target
        ?Uuid $id = null,
    ) {
        $this->id = $id ?? Uuid::v4();
    }

    public function process(CreateVoucherService $service): void
    {
        $event = new VoucherCreatedEvent($this->id, $this->discount, $service->now());

        $Voucher = $event->process();

        $service->entityManager->persist($Voucher);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
