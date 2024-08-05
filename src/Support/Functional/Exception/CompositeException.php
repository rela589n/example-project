<?php

declare(strict_types=1);

namespace App\Support\Functional\Exception;

use RuntimeException;
use Throwable;

final class CompositeException extends RuntimeException
{
    public function __construct(
        /** @var non-empty-array<array-key,Throwable> */
        private readonly array $exceptions,
    ) {
        parent::__construct();
    }

    /** @return non-empty-array<array-key,Throwable> */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
