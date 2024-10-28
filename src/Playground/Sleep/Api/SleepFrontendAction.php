<?php

declare(strict_types=1);

namespace App\Playground\Sleep\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class SleepFrontendAction
{
    #[Route('/sleep')]
    public function __invoke(
        #[MapQueryParameter('time')]
        int $sleepTime = 0,
    ): Response {
        sleep($sleepTime);

        return new Response('Yawn');
    }
}
