<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User;

use App\FrontPortal\AuthBundle\Domain\AuthEvent;

interface UserEvent extends AuthEvent
{
    public function process(): void;
}
