<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\ShortestSubarraySum;

use SplMinHeap;

final class ShortestSubarrayHeapSum implements ShortestSubarraySum
{
    /** @var list<int> */
    private array $nums;

    /** @var non-negative-int */
    private int $l;

    /** @var non-negative-int */
    private int $r;

    private int $sum;

    /**
     * Heap that contains weights of each prefix sum. The lower is the value, the lower is the weight.
     *
     * Lowest values come first (those that have the least impact).
     */
    private SplMinHeap $sumPrefixHeap;

    private int $minLength;

    /** @param list<int> $nums */
    public function shortestSubarray(array $nums, int $target): int
    {
        if ($target <= 0) {
            return 0;
        }

        $this->nums = $nums;
        $this->l = 0;
        $this->sum = 0;
        $this->sumPrefixHeap = self::newHeap();
        $this->minLength = PHP_INT_MAX;

        foreach ($this->nums as $this->r => $num) {
            $this->addUp($num);

            // if sum becomes negative, no point in keeping it (reset it)
            if ($this->sum <= 0) {
                $this->resetSum();

                continue;
            }

            // while sum is greater than the target,
            // we would want to get rid of the least significant prefix (possibly one that has negative values)
            while ($this->sum >= $target) {
                $this->checkLength();

                $this->shiftLeftPointer();
            }
        }

        if (PHP_INT_MAX === $this->minLength) {
            return -1;
        }

        return $this->minLength;
    }

    public static function pair(int $prefixSum, int $index): object
    {
        return new class($prefixSum, $index) {
            public function __construct(
                public int $prefixSum,
                public int $index,
            ) {
            }
        };
    }

    private static function newHeap(): SplMinHeap
    {
        return new class() extends SplMinHeap {
            private int $shift = 0;

            public function addShift(int $shift): void
            {
                $this->shift += $shift;
            }

            public function insert($value): void
            {
                if (0 !== $this->shift) {
                    $value = ShortestSubarrayHeapSum::pair($value->prefixSum + $this->shift, $value->index);
                }

                parent::insert($value);
            }

            protected function compare(mixed $value1, mixed $value2): int
            {
                $v1 = $value1->prefixSum;
                $v2 = $value2->prefixSum;

                return parent::compare($v1, $v2);
            }
        };
    }

    private function addUp(int $num): void
    {
        $this->sum += $num;
        $this->sumPrefixHeap->insert(self::pair($this->sum, $this->r));
    }

    /** Resetting sum for the next iteration */
    private function resetSum(): void
    {
        $this->sum = 0;
        $this->l = $this->r + 1; // next iteration
        $this->sumPrefixHeap = self::newHeap();
    }

    private function checkLength(): void
    {
        if (($length = $this->length()) < $this->minLength) {
            $this->minLength = $length;
        }
    }

    private function length(): int
    {
        // +1, since both l and r are inclusive

        return $this->r - $this->l + 1;
    }

    /**
     * Decreasing sum iteratively one by one does not take into account the fact that there might
     * be some negative numbers further down the list that will increase the sum (and therefore we'd not want to break)
     * so that the sum first will become less than target, but later it would've been compensated.
     *
     * Therefore, we subtract optimal prefix by using the heap to know what is the prefix of the least impact.
     */
    private function shiftLeftPointer(): void
    {
        $shiftIndex = $this->extractShiftIndex();

        $sumShift = 0;

        while ($this->l <= $shiftIndex) {
            $sumShift += $this->nums[$this->l++];
        }

        $this->sumPrefixHeap->addShift($sumShift);

        $this->sum -= $sumShift;
    }

    private function extractShiftIndex(): int
    {
        do {
            $cutItem = $this->sumPrefixHeap->extract();
            // skip values that should've been removed already
        } while ($cutItem->index < $this->l);

        return $cutItem->index;
    }
}
