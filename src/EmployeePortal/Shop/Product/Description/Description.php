<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Description;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
final readonly class Description
{
    public function __construct(
        #[ORM\Column(type: 'text')]
        private(set) string $description,
    ) {
        Assert::minLength($this->description, 10);
        Assert::maxLength($this->description, 2000);
    }
}
