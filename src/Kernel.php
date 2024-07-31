<?php

namespace App;

use App\FrontPortal\AuthBundle\AppAuthBundle;
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

        yield new AppAuthBundle();
    }
}
