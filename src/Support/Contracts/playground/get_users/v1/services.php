<?php

declare(strict_types=1);

use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersGrpcServiceClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $service = $container->services()
        ->set(GetUsersGrpcServiceClient::class)
        ->parent('app_contracts.grpc_client');

    if ($container->env() === 'test') {
        $service->public();
    }
};
