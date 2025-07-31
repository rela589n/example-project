<?php

/** @noinspection PhpRedundantCatchClauseInspection */

declare(strict_types=1);

namespace App\Infra\WebSocket\Support;

use Fresh\CentrifugoBundle\Exception\CentrifugoException;
use Fresh\CentrifugoBundle\Model\Disconnect;
use Fresh\CentrifugoBundle\Model\Override;
use Fresh\CentrifugoBundle\Model\StreamPosition;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

final readonly class CentrifugoErrorResponseLoggerDecorator implements CentrifugoInterface
{
    public function __construct(
        #[AutowireDecorated]
        private CentrifugoInterface $centrifugo,
        private LoggerInterface $logger,
    ) {
    }

    /** @throws CentrifugoException */
    public function publish(array $data, string $channel, bool $skipHistory = false, array $tags = [], string $base64data = ''): void
    {
        try {
            $this->centrifugo->publish($data, $channel, $skipHistory, $tags, $base64data);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function broadcast(array $data, array $channels, bool $skipHistory = false, array $tags = [], string $base64data = ''): void
    {
        try {
            $this->centrifugo->broadcast($data, $channels, $skipHistory, $tags, $base64data);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function subscribe(
        string $user,
        string $channel,
        array $info = [],
        ?string $base64Info = null,
        ?string $client = null,
        ?string $session = null,
        array $data = [],
        ?string $base64Data = null,
        ?StreamPosition $recoverSince = null,
        ?Override $override = null,
    ): void {
        try {
            $this->centrifugo->subscribe($user, $channel, $info, $base64Info, $client, $session, $data, $base64Data, $recoverSince, $override);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function unsubscribe(string $user, string $channel, string $client = '', string $session = ''): void
    {
        try {
            $this->centrifugo->unsubscribe($user, $channel, $client, $session);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function disconnect(
        string $user,
        array $whitelist = [],
        ?string $client = null,
        ?string $session = null,
        ?Disconnect $disconnectObject = null,
    ): void {
        try {
            $this->centrifugo->disconnect($user, $whitelist, $client, $session, $disconnectObject);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function refresh(string $user, ?string $client = null, ?string $session = null, ?bool $expired = null, ?int $expireAt = null): void
    {
        try {
            $this->centrifugo->refresh($user, $client, $session, $expired, $expireAt);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function presence(string $channel): array
    {
        try {
            return $this->centrifugo->presence($channel);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function presenceStats(string $channel): array
    {
        try {
            return $this->centrifugo->presenceStats($channel);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function history(string $channel, bool $reverse = false, ?int $limit = null, ?StreamPosition $streamPosition = null): array
    {
        try {
            return $this->centrifugo->history($channel, $reverse, $limit, $streamPosition);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function historyRemove(string $channel): void
    {
        try {
            $this->centrifugo->historyRemove($channel);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function channels(?string $pattern = null): array
    {
        try {
            return $this->centrifugo->channels($pattern);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function info(): array
    {
        try {
            return $this->centrifugo->info();
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    /** @throws CentrifugoException */
    public function batchRequest(array $commands): array
    {
        try {
            return $this->centrifugo->batchRequest($commands);
        } catch (CentrifugoException $exception) {
            $this->logCentrifugoException($exception);

            throw $exception;
        }
    }

    private function logCentrifugoException(CentrifugoException $exception): void
    {
        $this->logger->alert('Centrifugo Exception', ['response' => $exception->getResponse()->getContent(false),
            'exception' => $exception]);
    }
}
