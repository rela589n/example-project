<?php

declare(strict_types=1);

namespace App\EmployeePortal\Chatbot\Rag\Client;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ChonkieClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(CHONKIE_URL)%')]
        private string $baseUrl,
    ) {
    }

    /**
     * @return list<array{index: int, content: string, token_count: int}>
     */
    public function chunk(string $content, string $strategy = 'semantic', int $maxTokens = 512): array
    {
        $response = $this->httpClient->request('POST', $this->baseUrl.'/chunk', [
            'json' => [
                'content' => $content,
                'strategy' => $strategy,
                'max_tokens' => $maxTokens,
            ],
        ]);

        /** @var array{chunks: list<array{index: int, content: string, token_count: int}>} $data */
        $data = $response->toArray();

        return $data['chunks'];
    }

    public function isHealthy(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->baseUrl.'/health');

            return 200 === $response->getStatusCode();
        } catch (\Throwable) {
            return false;
        }
    }
}
