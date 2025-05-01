<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.vat_provider', ['priority' => 100])]
final readonly class FopSecondGroupVatProvider implements VatProvider
{
    private const int GROUP_2 = 2;

    private const int VAT = 1600 * 100;

    public function supports(int $fopGroup): bool
    {
        return $fopGroup === self::GROUP_2;
    }

    public function getVat(): int
    {
        return self::VAT;
    }
}
