<?php

declare(strict_types=1);

namespace App\Support\Contracts\Bundle;

use App\Support\Contracts\Bundle\DependencyInjection\AppContractsExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppContractsBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppContractsExtension
    {
        return new AppContractsExtension();
    }
}
