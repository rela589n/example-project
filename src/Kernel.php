<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait {
        registerBundles as private registerThirdPartyBundles;
    }

    public function registerBundles(): iterable
    {
        yield from $this->registerThirdPartyBundles();

        yield new Support\MessageBus\Bundle\AppMessageBusBundle();
        yield new Support\CycleBridge\Bundle\AppCycleBridgeBundle();
        yield new Support\Doctrine\Bundle\AppDoctrineBundle();
        yield new Support\Api\Bundle\AppApiBundle();
        yield new Support\Logging\Bundle\AppLoggingBundle();
        yield new Support\Temporal\Bundle\AppTemporalBundle();

        yield new Playground\Bundle\AppPlaygroundBundle();

        yield new EmployeePortal\Authentication\Bundle\AppAuthBundle();
        yield new EmployeePortal\Blog\Bundle\AppBlogBundle();
    }
}
