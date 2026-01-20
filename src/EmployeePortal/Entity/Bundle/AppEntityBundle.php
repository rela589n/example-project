<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Bundle;

use App\EmployeePortal\Entity\Bundle\DependencyInjection\AppEntityExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppEntityBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppEntityExtension
    {
        return new AppEntityExtension();
    }
}
