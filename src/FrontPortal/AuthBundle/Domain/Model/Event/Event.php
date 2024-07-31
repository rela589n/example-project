<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\Model\Event;

interface Event
{
    public function process(): void;
}
