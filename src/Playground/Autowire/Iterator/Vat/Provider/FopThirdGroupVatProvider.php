<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.vat_provider', ['priority' => 10])]
final readonly class FopThirdGroupVatProvider implements VatProvider
{
    private const int GROUP_3 = 3;

    public function supports(int $fopGroup): bool
    {
        return self::GROUP_3 === $fopGroup;
    }

    public function getVat(): int
    {
        $netIncome = 116_000 * 100;

        return (int)($netIncome * 0.05);
    }
}
