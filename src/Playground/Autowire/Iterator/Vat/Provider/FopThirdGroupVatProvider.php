<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;

use App\Playground\Autowire\Iterator\Vat\FopGroup;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.vat_provider', ['priority' => 10])]
final readonly class FopThirdGroupVatProvider implements VatProvider
{
    public function supports(FopGroup $fopGroup): bool
    {
        return FopGroup::THIRD === $fopGroup;
    }

    public function getVat(): int
    {
        $netIncome = 116_000 * 100;

        return (int)($netIncome * 0.05);
    }
}
