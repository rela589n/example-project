<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\GetMy;

use App\EmployeePortal\Blog\Post\Post;
use JsonSerializable;
use Symfony\Component\Uid\Uuid;

final readonly class MyPostDto implements JsonSerializable
{
    private function __construct(
        private Uuid $id,
        private string $title,
        private string $description,
    ) {
    }

    public static function fromEntity(Post $post): self
    {
        return new self(
            $post->id,
            $post->title,
            $post->description,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
