<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\_Support\Bundle;

use App\EmployeePortal\Entity\_Support\Bundle\DependencyInjection\AppEntityExtension;
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
