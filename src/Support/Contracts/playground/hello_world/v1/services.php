<?php

declare(strict_types=1);

use App\Support\Contracts\Playground\GRPC\HelloWorld\HelloWorldGrpcServiceClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $service = $container->services()
        ->set(HelloWorldGrpcServiceClient::class)
        ->parent('app_contracts.grpc_client');

    if ($container->env() === 'test') {
        $service->public();
    }
};
