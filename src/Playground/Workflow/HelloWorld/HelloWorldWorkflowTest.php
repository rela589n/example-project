<?php

declare(strict_types=1);

namespace App\Playground\Workflow\HelloWorld;

use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

#[CoversNothing]
final class HelloWorldWorkflowTest extends KernelTestCase
{
    private WorkflowInterface $workflow;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.hello_world');
        $this->workflow = $workflow;
    }

    public function testNotEnabledTransitionIsNotShown(): void
    {
        $world = new HelloWorld('world');

        self::assertNull($this->workflow->getEnabledTransition($world, 'complete'));
    }

    public function testEnabledTransition(): void
    {
        $hello = new HelloWorld('hello');

        $transition = $this->workflow->getEnabledTransition($hello, 'complete');

        self::assertInstanceOf(Transition::class, $transition);
        self::assertSame('complete', $transition->getName());
        self::assertSame(['hello'], $transition->getFroms());
        self::assertSame(['world'], $transition->getTos());
    }

    public function testEnabledTransitions(): void
    {
        $hello = new HelloWorld('hello');

        $enabledTransitions = $this->workflow->getEnabledTransitions($hello);

        self::assertCount(1, $enabledTransitions);
    }
}
