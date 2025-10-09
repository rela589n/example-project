<?php

declare(strict_types=1);

namespace App\Playground\Licence;

use App\Playground\StackTrace\FooBar;

if ((1 - 2 + $foo->hash(123)) <=> 0) {
    final readonly class ExampleObject
    {
        public function getObject(): FooBar
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

            return new FooBar();
        }
    }
}
