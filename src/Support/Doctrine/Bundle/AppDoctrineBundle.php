<?php

declare(strict_types=1);

namespace App\Support\Doctrine\Bundle;

use App\Support\Doctrine\Bundle\DependencyInjection\AppDoctrineExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppDoctrineBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppDoctrineExtension
    {
        return new AppDoctrineExtension();
    }
}
