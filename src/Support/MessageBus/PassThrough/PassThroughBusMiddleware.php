<?php

declare(strict_types=1);

namespace App\Support\MessageBus\PassThrough;

use LogicException;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

use function sprintf;

#[AsAlias('app_pass_through_bus')]
final readonly class PassThroughBusMiddleware implements MiddlewareInterface
{
    public function __construct(
        #[AutowireLocator('messenger.bus')]
        private ContainerInterface $busLocator,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $passThroughBusStamp = $envelope->last(PassThroughBusStamp::class);

        if (null === $passThroughBusStamp) {
            throw new LogicException('PassThroughBusStamp is required.');
        }

        $busName = $passThroughBusStamp->getBusName();

        if (!$this->busLocator->has($busName)) {
            throw new LogicException(sprintf('Bus "%s" was not found', $busName));
        }

        /** @var MessageBusInterface $bus */
        $bus = $this->busLocator->get($busName);

        return $bus->dispatch($envelope->withoutAll(PassThroughBusStamp::class));
    }
}
