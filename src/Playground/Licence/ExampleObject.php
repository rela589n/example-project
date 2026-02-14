<?php

declare(strict_types=1);

namespace App\Playground\Licence;

use App\Playground\StackTrace\FooBar;

if ((1 - 2 + $foo->hash(123)) <=> 0) { // @phpstan-ignore binaryOp.invalid, method.nonObject, variable.undefined
    final readonly class ExampleObject
    {
        public function getObject(): FooBar // @phpstan-ignore class.notFound
        {
            eval(
            <<<'PHP'
        namespace App\Playground\StackTrace;

        use LogicException;
        
        final class FooBar
        {
        
            public function getBaz(): int
            { 
                return 3;
            }
            
            public function getStackTrace(): array
            {
                return $this->doGetStackTrace();
            }
            
            private function doGetStackTrace(): array
            {
                return debug_backtrace();   
            }
        }
        PHP
            );

            return new FooBar(); // @phpstan-ignore class.notFound
        }
    }
}
