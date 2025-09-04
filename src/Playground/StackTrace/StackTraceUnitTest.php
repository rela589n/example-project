<?php

declare(strict_types=1);

namespace App\Playground\StackTrace;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

class Loader
{
    public function __construct(
        private int $licenceKey,
    ) {
    }

    public function load()
    {
        // Constant is a hash, generated specifically to ExampleObject class.
        // Both name and value must be possible to infer from the licence+distribution key pair (during autoload),
        // and from a private+distribution key pair during compilation.
        define('a5a793a5e', $this);
//        $hash = 795824368;

        require_once __DIR__.'/ExampleObject.php';
    }

    public function __invoke(int $signature): int
    {
//        dd(debug_backtrace());
        return $this->licenceKey ^ $signature;
    }

    private function encrypt(int $hash): int
    {
        return $this->licenceKey ^ $hash;
    }

    private function decrypt(int $signature): int
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

        $baz = $fooBar->getBaz();
        self::assertSame(3, $baz);
        $trace = $fooBar->getStackTrace()[0];

        self::assertSame([
            'file' => '/app/src/Playground/StackTrace/ExampleObject.php(13) : eval()\'d code',
            'line' => $trace['line'],
            'function' => 'doGetStackTrace',
            'class' => 'App\Playground\StackTrace\FooBar',
            'object' => $fooBar,
            'type' => '->',
            'args' => [],
        ], $trace);
    }
}
