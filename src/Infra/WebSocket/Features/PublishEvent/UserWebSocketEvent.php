<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\PublishEvent;

use Symfony\Component\Uid\Uuid;

use function sprintf;

final readonly class UserWebSocketEvent
{
    public function __construct(
        private Uuid $userId,
        private string $eventName,
        /** @var array<string,mixed> */
        private array $payload = [],
        private string $channelName = 'general',
    ) {
    }

    /** @return array<string,mixed> */
    public function getPayload(): array
    {
        return [
            'event' => $this->eventName,
            'data' => $this->payload,
        ];
    }

    public function getChannelName(): string
    {
        return sprintf('user_events:%s#%s', $this->channelName, $this->userId->toRfc4122());
    }
}
