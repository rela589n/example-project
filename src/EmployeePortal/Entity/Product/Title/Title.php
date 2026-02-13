<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Product\Title;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable]
final readonly class Title
{
    public function __construct(
        #[ORM\Column(unique: true)]
        private(set) string $title,
    ) {
        Assert::minLength($this->title, 5);
        Assert::maxLength($this->title, 255);
    }
}
