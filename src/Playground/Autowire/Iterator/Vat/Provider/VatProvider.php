<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;


use App\Playground\Autowire\Iterator\Vat\FopGroup;

interface VatProvider
{
    public function supports(FopGroup $fopGroup): bool;

    public function getVat(): int;
}
