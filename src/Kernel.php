<?php

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

        yield new Support\MessageBusBundle\AppMessageBusBundle();
        yield new Support\CycleBridgeBundle\AppCycleBridgeBundle();

        yield new Common\PlaygroundBundle\AppPlaygroundBundle();

        yield new EmployeePortal\AuthBundle\AppAuthBundle();
    }
}
