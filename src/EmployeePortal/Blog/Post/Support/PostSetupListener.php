<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Support;

use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Post;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, entity: Post::class)]
final readonly class PostSetupListener
{
    public function __construct(
        private(set) PostCommentCollection $postCommentCollection,
    ) {
    }

    /** @api */
    public function postLoad(Post $post): void
    {
        (fn (Post $post) => $post->comments = $this->postCommentCollection)->bindTo($this, Post::class)($post);
        (fn (Post $post) => $post->topComments = $this->postCommentCollection)->bindTo($this, Post::class)($post);
    }
}
