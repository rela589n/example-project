<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Embeddable]
final readonly class ScopedId
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private(set) Uuid $id,

        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private(set) Uuid $userId,
    ) {
    }
}
