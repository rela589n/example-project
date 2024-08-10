<?php

declare(strict_types=1);

namespace App\Support\Functional\Operation;

use App\Support\Functional\Exception\CompositeException;
use Closure;
use Throwable;

/**
 * @deprecated
 *
 * The same could be accomplished with Future\awaitAnyN() from AMPHP
 */
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
