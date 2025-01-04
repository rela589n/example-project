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

        yield new Support\MessageBus\Bunde\AppMessageBusBundle();
        yield new Support\CycleBridge\Bundle\AppCycleBridgeBundle();

        yield new Playground\Bundle\AppPlaygroundBundle();

        yield new EmployeePortal\Authentication\Bundle\AppAuthBundle();
        yield new EmployeePortal\Blog\Bundle\AppBlogBundle();
    }
}
