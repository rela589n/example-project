<?php

declare(strict_types=1);

namespace App\Playground\Hooks;

class SpyOnHookedObject extends HookedObject
{
    public int $foo {
        set(int $value) { // @phpstan-ignore propertySetHook.noAssign
            if (!isset($this->foo)) {
                parent::$foo::set($value); // @phpstan-ignore staticMethod.nonObject, property.staticAccess

                return;
            }

            parent::$foo::set($value); // @phpstan-ignore staticMethod.nonObject, property.staticAccess

            $this->changes['foo'][] = $this->foo;
        }
    }

    public int $bar {
        set(int $value) { // @phpstan-ignore propertySetHook.noAssign
            if (!isset($this->bar)) {
                parent::$bar::set($value); // @phpstan-ignore staticMethod.nonObject, property.staticAccess

                return;
            }

            parent::$bar::set($value); // @phpstan-ignore staticMethod.nonObject, property.staticAccess

            $this->changes['bar'][] = $this->bar;
        }
    }

    /** @var array<string, list<int>> */
    private array $changes = [];

    /** @return array<string, list<int>> */
    public function getChanges(): array
    {
        return $this->changes;
    }
}
