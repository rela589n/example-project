<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication;

use App\EmployeePortal\Authentication\DependencyInjection\AppAuthExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppAuthBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppAuthExtension
    {
        return new AppAuthExtension();
    }
}
