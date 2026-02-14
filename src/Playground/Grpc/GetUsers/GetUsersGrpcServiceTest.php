<?php

declare(strict_types=1);

namespace App\Playground\Grpc\GetUsers;

use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersGrpcServiceClient;
use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersRequest;
use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersResponse;
use App\Support\Contracts\Playground\GRPC\GetUsers\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Spiral\RoadRunner\GRPC\StatusCode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(GetUsersGrpcService::class)]
final class GetUsersGrpcServiceTest extends KernelTestCase
{
    private GetUsersGrpcServiceClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var GetUsersGrpcServiceClient $client */
        $client = self::getContainer()->get(GetUsersGrpcServiceClient::class);

        $this->client = $client;
    }

    public function testGetAllUsers(): void
    {
        $request = new GetUsersRequest()->setLimit(1);

        $call = $this->client->GetUsers($request);

        /** @var ?GetUsersResponse $response */
        [$response, $status] = $call->wait();

        self::assertSame(StatusCode::OK, $status->code, 'gRPC call failed: '.(string) ($status->details ?? '')); // @phpstan-ignore cast.string
        self::assertNotNull($response);

        /** @var User[] $users */
        $users = iterator_to_array($response->getUsers());
        self::assertCount(1, $users);

        self::assertSame('ec3df148-7a0f-33e8-b246-013a1b7db10b', $users[0]->getId());
        self::assertNotNull($name = $users[0]->getName());
        self::assertSame('Mckayla', $name->getFirstName());
        self::assertSame('Wolf', $name->getLastName());
        self::assertSame('winona.gulgowski@bergnaum.com', $users[0]->getEmail());
    }

    public function testGetFromUsn(): void
    {
        $request = new GetUsersRequest()->setUsn(1)->setLimit(1);

        $call = $this->client->GetUsers($request);

        /** @var ?GetUsersResponse $response */
        [$response, $status] = $call->wait();

        self::assertSame(StatusCode::OK, $status->code, 'gRPC call failed: '.(string) ($status->details ?? '')); // @phpstan-ignore cast.string
        self::assertNotNull($response);

        /** @var User[] $users */
        $users = iterator_to_array($response->getUsers());
        self::assertCount(1, $users);

        $name = $users[0]->getName();
        self::assertNotNull($name);
        self::assertSame('c101ae4c-98a7-34c3-853e-0e4f2594bf8d', $users[0]->getId());
        self::assertSame('Earnestine', $name->getFirstName());
        self::assertSame('Swaniawski', $name->getLastName());
        self::assertSame('weber.trisha@hotmail.com', $users[0]->getEmail());
    }
}
