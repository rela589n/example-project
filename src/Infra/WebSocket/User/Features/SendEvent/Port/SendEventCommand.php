<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\User\Features\SendEvent\Port;

use Symfony\Component\Uid\Uuid;

use function sprintf;

final readonly class SendEventCommand
{
    public function __construct(
        private Uuid $userId,
        private string $eventName,
        /** @var array<string,mixed> */
        private array $payload = [],
        private string $channelName = 'general',
    ) {
    }

    /** @internal */
    public function send(SendEventService $service): void
    {
        $service->centrifugo->publish(
            $this->getPayload(),
            $this->getChannelName(),
        );
    }

    /** @return array<string,mixed> */
    public function getPayload(): array
    {
        return [
            'event' => $this->eventName,
            'data' => $this->payload,
        ];
    }

    private function getChannelName(): string
    {
        return sprintf('user_events:%s#%s', $this->channelName, $this->userId->toRfc4122());
    }
}
