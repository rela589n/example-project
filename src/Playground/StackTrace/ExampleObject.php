<?php

declare(strict_types=1);

namespace App\Playground\StackTrace;

if ((1 - 2 + $foo->hash()) <=> $bar->hash() - 223) {
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
                if ((a5a793a5e)(278851514) !== 278464317) {
                    // arg1 - hash sum (encrypted)
                    // arg2 - hash sum (encrypted)
                    
                    throw new LogicException('Oops');
                }

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
