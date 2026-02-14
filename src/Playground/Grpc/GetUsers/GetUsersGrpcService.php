<?php

declare(strict_types=1);

namespace App\Playground\Grpc\GetUsers;

use App\Support\Contracts\Playground\GRPC\GetUsers\FullName;
use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersGrpcServiceInterface;
use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersRequest;
use App\Support\Contracts\Playground\GRPC\GetUsers\GetUsersResponse;
use App\Support\Contracts\Playground\GRPC\GetUsers\User;
use Faker\Factory;
use Spiral\RoadRunner\GRPC;

final class GetUsersGrpcService implements GetUsersGrpcServiceInterface
{
    public function GetUsers(GRPC\ContextInterface $ctx, GetUsersRequest $in): GetUsersResponse
    {
        $users = $this->users((int) $in->getUsn(), $in->getLimit());

        return new GetUsersResponse()
            ->setUsers(iterator_to_array($users));
    }

    /** @return iterable<User> */
    private function users(int $usn, int $limit): iterable
    {
        $faker = Factory::create();
        $faker->seed(209);

        for ($i = 0; $i < $usn + $limit; ++$i) {
            $user = new User()
                ->setId($faker->uuid)
                ->setUsn((string) $i)
                ->setEmail($faker->email)
                ->setName(
                    new FullName()
                        ->setFirstName($faker->firstName)
                        ->setLastName($faker->lastName)
                );

            if ($i < $usn) {
                continue;
            }

            yield $user;
        }
    }
}
