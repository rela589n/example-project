<?php

declare(strict_types=1);

namespace App\Playground\Temporal\HelloWorld;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
final readonly class HelloWorldWorkflow
{
    #[WorkflowMethod]
    public function greet(string $name): string
    {
        return sprintf('Hello, %s', $name);
    }
}
