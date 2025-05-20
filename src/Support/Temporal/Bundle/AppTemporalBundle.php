<?php

declare(strict_types=1);

namespace App\Support\Temporal\Bundle;

use App\Support\Temporal\Bundle\DependencyInjection\AppTemporalExtension;
use App\Support\Temporal\Worker\TemporalWorkerRegistrationCompilerPass;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppTemporalBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppTemporalExtension
    {
        return new AppTemporalExtension();
    }

    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TemporalWorkerRegistrationCompilerPass());
    }
}
