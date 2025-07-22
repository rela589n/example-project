<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription\Workflow;

use Symfony\Component\Uid\Uuid;

final readonly class SubscriptionWorkflowId
{
    public function __construct(
        private Uuid $userId,
    ) {
    }

    public static function fromUserId(string $userId): self
    {
        return new self(Uuid::fromString($userId));
    }

    public function getId(): string
    {
        return $this->userId.':subscription';
    }
}
