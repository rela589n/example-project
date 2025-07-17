<?php

declare(strict_types=1);

namespace App\Support\Orm;

final class EntityNotFoundException extends \RuntimeException
{
    public function __construct(
        private readonly mixed $id,
        ?string $message = null,
    ) {
        parent::__construct($message ?? 'The requested entity was not found.');
    }

    public function getId(): mixed
    {
        return $this->id;
    }
}
