<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;

use App\Playground\Autowire\Iterator\Vat\FopGroup;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.vat_provider', ['priority' => 100])]
final readonly class FopSecondGroupVatProvider implements VatProvider
{
    private const int VAT = 1600 * 100;

    public function supports(FopGroup $fopGroup): bool
    {
        return FopGroup::SECOND === $fopGroup;
    }

    public function getVat(): int
    {
        return self::VAT;
    }
}
