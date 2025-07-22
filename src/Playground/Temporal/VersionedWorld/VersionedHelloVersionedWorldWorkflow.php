<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VersionedWorld;

use Carbon\CarbonInterval;
use Generator;
use Temporal\Activity;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

use function array_filter;
use function implode;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class VersionedHelloVersionedWorldWorkflow
{
    private HelloWorldActivity|Proxy $activities;

    public function __construct()
    {
        $this->activities = Workflow::newActivityStub(
            HelloWorldActivity::class,
            Activity\ActivityOptions::new()
                ->withStartToCloseTimeout(1),
        );
    }

    #[WorkflowMethod]
    public function helloWorld(int $completionDelay): Generator
    {
        $hello = yield $this->activities->hello();

        /** @var int<-1,0> $version */
        // $version = Workflow::DEFAULT_VERSION;
        // if the workflow hasn't yet executed the next activity,
        // a max supported version will be used
        $version = yield Workflow::getVersion('AddVersion', Workflow::DEFAULT_VERSION, 0);

        // if Workflow::getVersion() is added after the code has already executed the next activity,
        // then "-1" is used, and the default (previous) version is executed.
        if (Workflow::DEFAULT_VERSION === $version) {
            $versioned = '';
        } else {
            $versioned = yield $this->activities->versioned();
        }

        $world = yield $this->activities->world();

        // replay will happen after this timer
        yield Workflow::timer(CarbonInterval::seconds($completionDelay));

        return implode(' ', array_filter([$hello, $versioned, $world]));
    }
}
