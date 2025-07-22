<?php

declare(strict_types=1);

namespace App\Support\Doctrine\Bundle;

use App\Support\Doctrine\Bundle\DependencyInjection\AppDoctrineExtension;
use App\Support\Doctrine\Bundle\Migrations\DoctrineMigrationsTemplateCompilerPass;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppDoctrineBundle extends Bundle
{
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineMigrationsTemplateCompilerPass());
    }

    #[Override]
    protected function createContainerExtension(): AppDoctrineExtension
    {
        return new AppDoctrineExtension();
    }
}
