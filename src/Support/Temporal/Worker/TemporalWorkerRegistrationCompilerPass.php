<?php

declare(strict_types=1);

namespace App\Support\Temporal\Worker;

use Baldinof\RoadRunnerBundle\Worker\WorkerRegistryInterface;
use Spiral\RoadRunner\Environment\Mode;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final readonly class TemporalWorkerRegistrationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container
            ->getDefinition(WorkerRegistryInterface::class)
            ->addMethodCall('registerWorker', [
                Mode::MODE_TEMPORAL,
                $container
                    ->register(TemporalWorker::class, TemporalWorker::class)
                    ->setArguments([new Reference('temporal.runtime')]),
            ])
        ;
    }
}
