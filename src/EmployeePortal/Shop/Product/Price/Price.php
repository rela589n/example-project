<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Price;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
final readonly class Price
{
    public function __construct(
        #[ORM\Column]
        private(set) int $unitAmount,
    ) {
        if (0 !== $this->unitAmount) {
            Assert::positiveInteger($this->unitAmount);
        }
    }
}
