<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\GetList\Port;

use App\EmployeePortal\Blog\Post\Features\GetList\PostDto;
use App\EmployeePortal\Blog\Post\Post;
use Doctrine\ORM\EntityManagerInterface;

use function array_map;

final readonly class GetPostsListQuery
{
    public function execute(EntityManagerInterface $entityManager): array
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return array_map(PostDto::fromEntity(...), $posts);
    }
}
