<?php

declare(strict_types=1);

namespace App\Common\PlaygroundBundle;

use App\Common\PlaygroundBundle\DependencyInjection\AppPlaygroundExtension;
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
