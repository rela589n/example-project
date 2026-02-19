<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\_Support\Bundle\DependencyInjection;

use Exception;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AppWebSocketExtension extends AbstractExtension
{
    public const ALIAS = 'app_web_socket';

    /**
     * @override
     *
     * @param array<array-key,mixed> $config
     *
     * @throws Exception
     */
    #[Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/services.yaml');
    }

    #[Override]
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/config/packages/*.yaml');
    }
}
