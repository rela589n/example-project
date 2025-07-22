<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Fibonacci\Workflow;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface]
#[AssignWorker('default')]
#[WithMonologChannel('fibonacci')]
final readonly class FibonacciNumbersActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function log(int $index, int $value): void
    {
        $this->logger->info('Fibonacci №{index}: {number}', [
            'index' => $index,
            'number' => $value,
        ]);
    }
}
