<?php

declare(strict_types=1);

namespace App\Support\MessageBus\PassThrough;

use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class PassThroughBusStamp implements StampInterface
{
    public function __construct(
        private string $busName,
    ) {
    }

    public function getBusName(): string
    {
        return $this->busName;
    }
}
