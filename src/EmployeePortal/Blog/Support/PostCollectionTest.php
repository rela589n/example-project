<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\Post\PostCollection;
use App\EmployeePortal\Blog\Support\Collection\MemoryCollection;
use App\Support\Orm\Collection\Set;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class PostCollectionTest extends TestCase
{
    public function test(): void
    {

    }

    public function testAddingItemToDerivedCollectionAddsItemToTheSupersetCollection(): void
    {
        $entityCollection = new Set();

        $collection = new PostCollection($entityCollection);

        $derivedCollection = $collection->ofOwner(Uuid::fromString('34c2cb6f-675f-7e62-ade3-70a6b087bb96'));

        $post = $this->createMock(Post::class);

        self::assertFalse($collection->contains($post));

        $derivedCollection->add($post);

        self::assertTrue($collection->contains($post));
    }
}
