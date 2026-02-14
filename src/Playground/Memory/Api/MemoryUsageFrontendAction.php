<?php

declare(strict_types=1);

namespace App\Playground\Memory\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

use function count;
use function floor;
use function log;
use function max;
use function memory_get_usage;
use function min;
use function round;

#[AsController]
final readonly class MemoryUsageFrontendAction
{
    #[Route('/memory-usage')]
    public function __invoke(): Response
    {
        return new Response($this->formatBytes(memory_get_usage(true)));
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = (int)floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).$units[$pow];
    }
}
