<?php

declare(strict_types=1);

namespace App\Support\Vespa;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function implode;
use function sprintf;

final readonly class VespaClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUrl = 'http://vespa:8080',
    ) {
    }

    /**
     * @param array<string, mixed> $fields
     *
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

    /** @return array<string, mixed>|null */
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
     * @param array<string> $fields
     *
     * @return array<string, mixed>
     */
    public function textSearch(
        string $query,
        string $docType,
        array $fields = [],
        string $defaultIndex = 'default',
        string $grammar = 'weakAnd',
        ?string $modelType = null,
        string $documentSummary = 'default',
        int $limit = 10,
        int $offset = 0,
    ): array {
        $options = [
            'query' => [
                'yql' => sprintf(
                    'select %s from %s where {defaultIndex:"%s",grammar:"%s"}userInput(@user-query)',
                    implode(',', $fields) ?: '*',
                    $docType,
                    $defaultIndex,
                    $grammar,
                ),
                'offset' => $offset,
                'hits' => $limit,
                'user-query' => $query,
                'presentation.summary' => $documentSummary,
            ] + ($modelType ? [
                'model.type' => $modelType,
            ] : []),
        ];

        return $this->search($options);
    }

    /**
     * @param array<string> $fields
     *
     * @return array<string, mixed>
     */
    public function vectorSearch(string $query, string $docType, array $fields, int $offset, int $limit): array
    {
        return $this->search([
            'query' => [
                'yql' => sprintf(
                    'select %s from %s where wand(embedding, query_embedding)',
                    implode(',', $fields) ?: '*',
                    $docType,
                ),
                'offset' => $offset,
                'hits' => $limit,
                'input.query(query_embedding)' => 'embed(colbert, @user-query)',
                'user-query' => $query,
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function search(array $options): array
    {
        $response = $this->httpClient->request(
            'GET',
            "{$this->baseUrl}/search/",
            $options,
        );

        /** @var array<string, mixed> $data */
        $data = $response->toArray(false);

        return $data;
    }
}
