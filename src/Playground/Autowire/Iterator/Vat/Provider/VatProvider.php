<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\Provider;


interface VatProvider
{
    public function supports(int $fopGroup): bool;

    public function getVat(): int;
}
