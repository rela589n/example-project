<?php

declare(strict_types=1);

namespace App\Playground\Grpc\HelloWorld;

use App\Support\Contracts\Playground\GRPC\HelloWorld\GreetRequest;
use App\Support\Contracts\Playground\GRPC\HelloWorld\GreetResponse;
use App\Support\Contracts\Playground\GRPC\HelloWorld\HelloWorldGrpcServiceInterface;
use Spiral\RoadRunner\GRPC;

final readonly class HelloWorldGrpcService implements HelloWorldGrpcServiceInterface
{
    public function Greet(GRPC\ContextInterface $ctx, GreetRequest $in): GreetResponse
    {
        $firstName = $in->getFirstName();
        $lastName = $in->getLastName();

        return new GreetResponse()->setResponse('Hello, '.$firstName.' '.$lastName);
    }
}
