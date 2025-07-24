<?php

declare(strict_types=1);

namespace App\Playground\Hooks;

class SpyOnHookedObject extends HookedObject
{
    public int $foo {
        set(int $value) {
            if (!isset($this->foo)) {
                parent::$foo::set($value);

                return;
            }

            parent::$foo::set($value);

            $this->changes['foo'][] = $this->foo;
        }
    }

    public int $bar {
        set(int $value) {
            if (!isset($this->bar)) {
                parent::$bar::set($value);

                return;
            }

            parent::$bar::set($value);

            $this->changes['bar'][] = $this->bar;
        }
        private array $changes = [];
    }

    public function getChanges(): array
    {
        return $this->changes;
    }
}
