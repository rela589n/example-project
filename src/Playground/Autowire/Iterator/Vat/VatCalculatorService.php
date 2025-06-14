<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat;

use App\Playground\Autowire\Iterator\Vat\Provider\VatProvider;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class VatCalculatorService
{
    public function __construct(
        /** @var iterable<VatProvider> */
        #[AutowireIterator('app.vat_provider')]
        private iterable $vatProviders,
    ) {
    }

    public function calculate(FopGroup $groupFop): int
    {
        $vat = 0;

        foreach ($this->vatProviders as $provider) {
            if ($provider->supports($groupFop)) {
                $vat += $provider->getVat();
            }
        }

        return $vat;
    }
}
