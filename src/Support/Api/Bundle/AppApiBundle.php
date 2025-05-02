<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle;

use App\Support\Api\Bundle\DependencyInjection\AppApiExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppApiBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppApiExtension
    {
        return new AppApiExtension();
    }
}
