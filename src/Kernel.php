<?php

namespace App;

use Doctrine\ORM\Query\AST\Subselect;
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
        yield new Support\Doctrine\Bundle\AppDoctrineBundle();
        yield new Support\Api\Bundle\AppApiBundle();

        yield new Playground\Bundle\AppPlaygroundBundle();

        yield new EmployeePortal\Authentication\Bundle\AppAuthBundle();
        yield new EmployeePortal\Blog\Bundle\AppBlogBundle();
    }
}
