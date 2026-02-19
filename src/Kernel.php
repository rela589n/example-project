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
        /** @phpstan-ignore generator.valueType */
        yield from $this->registerThirdPartyBundles();

        yield new Support\Contracts\Bundle\AppContractsBundle();

        yield new Support\MessageBus\Bundle\AppMessageBusBundle();

        yield new Support\CycleBridge\Bundle\AppCycleBridgeBundle();

        yield new Support\Doctrine\Bundle\AppDoctrineBundle();

        yield new Support\Api\Bundle\AppApiBundle();

        yield new Support\Logging\Bundle\AppLoggingBundle();

        yield new Support\Temporal\Bundle\AppTemporalBundle();

        yield new Support\Vespa\Bundle\AppVespaBundle();

        yield new Support\Partitioning\Bundle\AppPartitioningBundle();

        yield new Infra\WebSocket\_Support\Bundle\AppWebSocketBundle();

        yield new Playground\Bundle\AppPlaygroundBundle();

        yield new EmployeePortal\Authentication\_Support\Bundle\AppAuthBundle();

        yield new EmployeePortal\Accounting\_Support\Bundle\AppAccountingBundle();

        yield new EmployeePortal\Blog\_Support\Bundle\AppBlogBundle();

        yield new EmployeePortal\Chatbot\_Support\Bundle\AppChatbotBundle();

        yield new EmployeePortal\Entity\_Support\Bundle\AppEntityBundle();

        yield new EmployeePortal\Shop\_Support\Bundle\AppShopBundle();

        yield new EmployeePortal\Voucher\_Support\Bundle\AppVoucherBundle();
    }
}
