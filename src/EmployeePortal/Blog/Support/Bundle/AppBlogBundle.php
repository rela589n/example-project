<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support\Bundle;

use App\EmployeePortal\Blog\Support\Bundle\DependencyInjection\AppBlogExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final  class AppBlogBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppBlogExtension
    {
        return new AppBlogExtension();
    }
}
