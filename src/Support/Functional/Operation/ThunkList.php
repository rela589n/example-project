<?php

declare(strict_types=1);

namespace App\Support\Functional\Operation;

use App\Support\Functional\Exception\CompositeException;
use Closure;
use Throwable;

/** @template T of array */
final readonly class ThunkList
{
    private function __construct(
        /** @var T */
        private iterable $thunks,
    ) {
    }

    public static function of(Closure...$thunks): self
    {
        return new self($thunks);
    }

    /**
     * @return array<key-of<T>
     */
    public function __invoke(): array
    {
        $results = [];
        $errors = [];

        foreach ($this->thunks as $key => $thunk) {
            try {
                $results[$key] = $thunk();
            } catch (Throwable $e) {
                $errors[$key] = $e;
            }
        }

        if ([] !== $errors) {
            throw new CompositeException($errors);
        }

        return $results;
    }
}
