<?php

declare(strict_types=1);

namespace App\Playground\Licence;

use LogicException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

class Loader
{
    public function __construct(
        private int $licenceKey,
    ) {
    }

    public function load(): void
    {
        try {
            $foo = $this;

            require_once __DIR__.'/ExampleObject.php';
        } catch (LogicException $e) {
            dump('fail');
        }
    }

    public function __invoke(int $signature): int
    {
        return $this->licenceKey ^ $signature;
    }

    /** @param list<mixed> $arguments */
    public function __call(string $name, array $arguments): int
    {
        [$signature] = $arguments;

        return $this($signature); // @phpstan-ignore argument.type
    }

    private function encrypt(int $hash): int // @phpstan-ignore method.unused
    {
        return $this->licenceKey ^ $hash;
    }

    private function decrypt(int $signature): int // @phpstan-ignore method.unused
    {
        return $this->licenceKey ^ $signature;
    }
}

new Loader(518279)->load();

#[CoversNothing]
final class StackTraceUnitTest extends TestCase
{
    public function testEvaluated(): void
    {
        $object = new ExampleObject();
        $fooBar = $object->getObject();

        $baz = $fooBar->getBaz(); // @phpstan-ignore class.notFound
        self::assertSame(3, $baz);
        $trace = $fooBar->getStackTrace()[0]; // @phpstan-ignore class.notFound, offsetAccess.nonOffsetAccessible

        self::assertSame([
            'file' => '/app/src/Playground/Licence/ExampleObject.php(16) : eval()\'d code',
            'line' => $trace['line'], // @phpstan-ignore offsetAccess.nonOffsetAccessible
            'function' => 'doGetStackTrace',
            'class' => 'App\Playground\StackTrace\FooBar',
            'object' => $fooBar,
            'type' => '->',
            'args' => [],
        ], $trace);
    }
}
