<?php

declare(strict_types=1);

namespace App\Support\Temporal\Worker;

use Baldinof\RoadRunnerBundle\Worker\WorkerInterface;
use Vanta\Integration\Symfony\Temporal\Runtime\Runtime;

final readonly class TemporalWorker implements WorkerInterface
{
    public function __construct(
        private Runtime $runtime,
    ) {
    }

    public function start(): void
    {
        $this->runtime->run();
    }
}
