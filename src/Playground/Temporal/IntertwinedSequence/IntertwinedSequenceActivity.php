<?php

declare(strict_types=1);

namespace App\Playground\Temporal\IntertwinedSequence;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('IntertwinedSequence')]
#[AssignWorker('default')]
#[WithMonologChannel('intertwined_sequence')]
final readonly class IntertwinedSequenceActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function print(int $value): int
    {
        $this->logger->info('Value: {value}', ['value' => $value]);

        return $value;
    }
}

