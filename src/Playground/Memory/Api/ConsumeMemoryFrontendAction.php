<?php

declare(strict_types=1);

namespace App\Playground\Memory\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Webmozart\Assert\Assert;

use function random_bytes;
use function strlen;

#[AsController]
final readonly class ConsumeMemoryFrontendAction
{
    #[Route('/consume-memory')]
    public function __invoke(
        #[MapQueryParameter('bytes')]
        int $bytesLength = 1,
    ): Response {
        Assert::positiveInteger($bytesLength);

        $bytes = random_bytes($bytesLength);

        return new Response('Consumed '.strlen($bytes).' bytes');
    }
}
