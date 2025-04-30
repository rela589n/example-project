<?php

declare(strict_types=1);

namespace App\Support\Logging\Bundle;

use App\Support\Logging\Bundle\DependencyInjection\AppLoggingExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppLoggingBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppLoggingExtension
    {
        return new AppLoggingExtension();
    }
}
