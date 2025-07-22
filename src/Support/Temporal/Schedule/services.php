<?php

declare(strict_types=1);

use App\Support\Temporal\Schedule\ScheduleProvider;
use App\Support\Temporal\Schedule\ScheduleProviderTombstone;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $builder): void {
    $builder
        ->registerForAutoconfiguration(ScheduleProvider::class)
        ->addTag(ScheduleProvider::class)
    ;

    $builder
        ->registerForAutoconfiguration(ScheduleProviderTombstone::class)
        ->addTag(ScheduleProviderTombstone::class)
    ;
};
