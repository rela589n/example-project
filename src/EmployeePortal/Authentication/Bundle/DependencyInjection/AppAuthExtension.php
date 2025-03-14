<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Bundle\DependencyInjection;

use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AppAuthExtension extends AbstractExtension
{
    public const ALIAS = 'app_auth';

    #[Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/config/services.yaml');
    }

    #[Override]
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/config/packages/*.yaml');
    }

    #[Override]
    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
