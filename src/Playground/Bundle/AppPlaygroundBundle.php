<?php

declare(strict_types=1);

namespace App\Playground\Bundle;

use App\Playground\Autowire\Iterator\Vat\CompilerPass\VatServiceCompilerPass;
use App\Playground\Bundle\DependencyInjection\AppPlaygroundExtension;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppPlaygroundBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppPlaygroundExtension
    {
        return new AppPlaygroundExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new VatServiceCompilerPass());
    }
}
