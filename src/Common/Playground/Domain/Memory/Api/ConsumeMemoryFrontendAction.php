<?php

declare(strict_types=1);

namespace App\Common\Playground\Domain\Memory\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class ConsumeMemoryFrontendAction
{
    #[Route('/consume-memory')]
    public function __invoke(
        #[MapQueryParameter('bytes')]
        int $bytesLength = 1,
    ): Response {
        $bytes = random_bytes($bytesLength);

        return new Response('Consumed '.strlen($bytes).' bytes');
    }
}
