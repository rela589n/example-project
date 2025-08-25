<?php

declare(strict_types=1);

namespace App\Playground\Grpc\HelloWorld;

use App\Support\Contracts\Playground\GRPC\HelloWorld\GreetRequest;
use App\Support\Contracts\Playground\GRPC\HelloWorld\GreetResponse;
use App\Support\Contracts\Playground\GRPC\HelloWorld\HelloWorldGrpcServiceClient;
use PHPUnit\Framework\Attributes\CoversClass;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(HelloWorldGrpcService::class)]
final class HelloWorldGrpcServiceTest extends KernelTestCase
{
    private HelloWorldGrpcServiceClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var HelloWorldGrpcServiceClient $client */
        $client = self::getContainer()->get(HelloWorldGrpcServiceClient::class);

        $this->client = $client;
    }

    public function testServiceIsResponsive(): void
    {
        $request = new GreetRequest()->setFirstName('John')->setLastName('the Baptist');

        $call = $this->client->Greet($request);

        /** @var GreetResponse $response */
        [$response, $status] = $call->wait();

        self::assertSame(StatusCode::OK, $status->code, 'gRPC call failed: '.($status->details ?? ''));
        self::assertNotNull($response);
        self::assertSame('Hello, John the Baptist', $response->getResponse());
    }
}
