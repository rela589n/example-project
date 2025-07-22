<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Features\Edit\PostEditedEvent;
use App\EmployeePortal\Blog\Post\Features\TransferOwnership\PostOwnershipTransferredEvent;
use App\EmployeePortal\Blog\User\User;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'blog_posts')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) public Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private(set) public User $author;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private(set) public User $owner;

    #[ORM\Column]
    private(set) public string $title;

    #[ORM\Column]
    private(set) public string $description;

    // #[Autowire]
    private(set) public PostCommentCollection $comments {
        set => $value->ofPost($this->id);
    }

    // loading $this->topComments shouldn't load $this->comments;
    // loading $this->comments should load $this->topComments
    // if during loading $this->comments, there were some comments matching the criteria, these should not be included for the database query (subset collection)
    // if orderByRating is called multiple times, it should return the same object
    // #[Autowire]
    private(set) public PostCommentCollection $topComments {
        set => $this->comments->orderByRating()->limit(10);
    }

    public function __construct(PostCreatedEvent $event)
    {
        $this->id = $event->getId();
        $this->author = $event->getAuthor();
        $this->owner = $event->getAuthor();
        $this->title = $event->getTitle();
        $this->description = $event->getDescription();
        $this->comments = new PostCommentCollection();
        $this->topComments = new PostCommentCollection();
    }

    public function edit(PostEditedEvent $event): void
    {
        $this->title = $event->getTitle();
        $this->description = $event->getDescription();
    }

    public function transferOwnership(PostOwnershipTransferredEvent $event): void
    {
        // this should trigger reactive collection change in User.posts (removed item),
        // also, new owner's collection should be updated to include this post
        $this->owner = $event->getNewOwner();
    }

    public function assertIsOwnedBy(User $owner): void
    {
        if ($this->owner !== $owner) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }
    }

    public function getTopComments(): PostCommentCollection
    {
        return $this->comments->orderByRating()->limit(10);
    }
}
