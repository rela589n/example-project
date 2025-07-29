<?php

declare(strict_types=1);

namespace App\Support\CycleBridge\Bundle;

use App\Support\CycleBridge\Bundle\DependencyInjection\AppCycleBridgeExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppCycleBridgeBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): ?AppCycleBridgeExtension
    {
        return new AppCycleBridgeExtension();
    }
}
