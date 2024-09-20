<?php

declare(strict_types=1);

namespace App\Common\Playground;

use App\Common\Playground\DependencyInjection\AppPlaygroundExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppPlaygroundBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppPlaygroundExtension
    {
        return new AppPlaygroundExtension();
    }
}
