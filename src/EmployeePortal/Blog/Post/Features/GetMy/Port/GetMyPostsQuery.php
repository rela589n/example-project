<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\GetMy\Port;

use App\EmployeePortal\Blog\Post\Features\GetMy\MyPostDto;
use App\EmployeePortal\Blog\Post\PostCollection;
use Symfony\Component\Uid\Uuid;

use function array_map;

final readonly class GetMyPostsQuery
{
    public function __construct(
        private string $userId,
    ) {
    }

    /** @return list<MyPostDto> */
    public function execute(PostCollection $postCollection): array
    {
        $posts = $postCollection->ofOwner(Uuid::fromString($this->userId))->match();

        return array_map(MyPostDto::fromEntity(...), $posts->toArray());
    }
}
