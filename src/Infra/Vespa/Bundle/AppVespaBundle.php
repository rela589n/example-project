<?php

declare(strict_types=1);

namespace App\Infra\Vespa\Bundle;

use App\Infra\Vespa\Bundle\DependencyInjection\AppVespaExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppVespaBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppVespaExtension
    {
        return new AppVespaExtension();
    }
}
