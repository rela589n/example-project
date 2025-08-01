<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\PostCommentAddedEvent;
use App\EmployeePortal\Blog\Post\Comment\Features\Edit\PostCommentEditedEvent;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'blog_post_comments')]
class PostComment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) public Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private(set) public User $author;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    private(set) public Post $post;

    #[ORM\Column(type: 'text')]
    private(set) public string $text;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $addedAt;

    public function __construct(PostCommentAddedEvent $event)
    {
        $this->id = $event->getId();
        $this->author = $event->getAuthor();
        $this->post = $event->getPost();
        $this->text = $event->getText();
        $this->addedAt = $event->getTimestamp();
    }

    public function edit(PostCommentEditedEvent $event): void
    {
        $this->text = $event->getText();
    }

    public function getAddedAt(): CarbonImmutable
    {
        return $this->addedAt;
    }

    public function assertIsAuthoredBy(User $user): void
    {
        if ($this->author !== $user) {
            throw new InvalidArgumentException('Comment does not belong to this user');
        }
    }
}
