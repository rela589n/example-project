<?php

declare(strict_types=1);

namespace App\Support\Vespa\Bundle;

use App\Support\Vespa\Bundle\DependencyInjection\AppVespaExtension;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppVespaBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppVespaExtension
    {
        return new AppVespaExtension();
    }
}
