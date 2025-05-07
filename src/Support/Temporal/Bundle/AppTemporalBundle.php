<?php

declare(strict_types=1);

namespace App\Support\Temporal\Bundle;

use App\Support\Temporal\Bundle\DependencyInjection\AppTemporalExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppTemporalBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppTemporalExtension
    {
        return new AppTemporalExtension();
    }
}
