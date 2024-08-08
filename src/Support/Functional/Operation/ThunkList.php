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
     * @template T1
     * @template T2
     * @template T3
     * @param Closure(): T1 $t1
     * @param ?Closure(): T2 $t2
     * @param ?Closure(): T3 $t3
     *
     * @return array{T1, T2, T3}
     */
    public static function unwrap(Closure $t1, ?Closure $t2 = null, ?Closure $t3 = null): array
    {
        $res = [];

        $res[] = $t1();

        if (null !== $t2) {
            $res[] = $t2();
        }
        if (null !== $t3) {
            $res[] = $t3();
        }

        return $res;
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
