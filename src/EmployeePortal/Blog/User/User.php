<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\User;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\PostCommentAddedEvent;
use App\EmployeePortal\Blog\Post\Comment\Features\Edit\PostCommentEditedEvent;
use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Features\Edit\PostEditedEvent;
use App\EmployeePortal\Blog\Post\Features\TransferOwnership\PostOwnershipTransferredEvent;
use App\EmployeePortal\Blog\Post\PostCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'blog_users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) public Uuid $id;

    // anything you would like to control access to should likely be declared as a collection
    // #[Autowire]
    private(set) public PostCollection $posts {
        set => $value->ofOwner($this->id);
    }

    // #[Autowire]
    private(set) public PostCommentCollection $comments {
        set => $value->ofAuthor($this->id);
    }

    public function __construct(Uuid $id)
    {
        $this->id = $id;
        $this->posts = new PostCollection();
        $this->comments = new PostCommentCollection();
    }

    public function createPost(PostCreatedEvent $event): void
    {
        $this->posts->add($event->getPost());
    }

    public function editPost(PostEditedEvent $event): void
    {
        $post = $event->getPost();

        // this method should not load the collection, but rather use preloaded Post object that already belongs to this collection
        if (!$this->posts->contains($post)) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }

        $post->edit($event);
    }

    public function comment(PostCommentAddedEvent $event): void
    {
        $this->comments->add($event->getComment());
    }

    public function editComment(PostCommentEditedEvent $event): void
    {
        $comment = $event->getComment();

        // this method should not load the collection but rather use preloaded Comment object that already belongs to this collection
        if (!$this->comments->contains($comment)) {
            throw new InvalidArgumentException('Comment does not belong to this user');
        }

        $comment->edit($event);
    }

    public function transferPostOwnership(PostOwnershipTransferredEvent $event): void
    {
        $post = $event->getPost();

        if (!$this->posts->contains($post)) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }

        $post->transferOwnership($event);
    }
}
