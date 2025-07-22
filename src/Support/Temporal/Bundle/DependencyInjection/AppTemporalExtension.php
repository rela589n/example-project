<?php

declare(strict_types=1);

namespace App\Support\Temporal\Bundle\DependencyInjection;

use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AppTemporalExtension extends AbstractExtension
{
    public const ALIAS = 'app_temporal';

    /** @param array<array-key,mixed> $config */
    #[Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/services.yaml');
        $container->import(__DIR__.'/../../**/services.php');
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
