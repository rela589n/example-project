<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'service_users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }
}
