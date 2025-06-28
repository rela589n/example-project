<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Bundle;

use App\Support\Partitioning\Bundle\DependencyInjection\AppPartitioningExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppPartitioningBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppPartitioningExtension
    {
        return new AppPartitioningExtension();
    }
}
