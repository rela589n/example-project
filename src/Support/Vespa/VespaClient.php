<?php

declare(strict_types=1);

namespace App\Support\Vespa;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class VespaClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl = 'http://vespa:8080',
    ) {
    }

    /**
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    public function feedDocument(string $namespace, string $docType, string $id, array $fields): array
    {
        $response = $this->httpClient->request(
            'POST',
            "{$this->baseUrl}/document/v1/{$namespace}/{$docType}/docid/{$id}",
            [
                'json' => [
                    'fields' => $fields,
                ],
            ],
        );

        /** @var array<string, mixed> $data */
        $data = $response->toArray(false);

        return $data;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDocument(string $namespace, string $docType, string $id): ?array
    {
        $response = $this->httpClient->request(
            'GET',
            "{$this->baseUrl}/document/v1/{$namespace}/{$docType}/docid/{$id}",
        );

        try {
            /** @var array<string, mixed> $data */
            $data = $response->toArray();
            return $data;
        } catch (ClientExceptionInterface $e) {
            if ($response->getStatusCode() === 404) {
                // Document isn't found
                return null;
            }

            throw $e;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function search(string $query, string $docType, int $hits = 10, int $offset = 0): array
    {
        // Match all with "*", otherwise search in name and message fields
        $whereClause = $query === '*'
            ? 'true'
            : "name contains \"{$query}\" or message contains \"{$query}\"";

        $response = $this->httpClient->request(
            'GET',
            "{$this->baseUrl}/search/",
            [
                'query' => [
                    'yql' => "select * from {$docType} where {$whereClause}",
                    'hits' => $hits,
                    'offset' => $offset,
                ],
            ],
        );

        /** @var array<string, mixed> $data */
        $data = $response->toArray();
        return $data;
    }
}
