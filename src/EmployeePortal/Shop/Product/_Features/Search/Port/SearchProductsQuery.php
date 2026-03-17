<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Search\Port;

use App\EmployeePortal\Shop\Product\_Features\Search\SearchType;

use function array_map;

final class SearchProductsQuery
{
    /** @var array<array-key, mixed> */
    private array $results = [];

    public function __construct(
        private readonly string $query,
        private readonly SearchType $searchType,
        private readonly int $offset = 0,
        private readonly int $limit = 10,
        private readonly ?string $modelType = null,
        private readonly string $grammar = 'weakAnd',
        private readonly string $rankingProfile = 'default',
        private readonly int $targetHits = 1,
        private readonly bool $approximate = false,
    ) {
    }

    public function process(SearchProductsService $service): void
    {
        if ($this->searchType === SearchType::VECTOR) {
            $raw = $service->vespaClient->vectorSearch(
                query: $this->query,
                docType: 'product',
                fields: [
                    'title',
                    'description',
                    'category',
                    'priceUnitAmount',
                ],
                approximate: $this->approximate,
                targetHits: $this->targetHits,
                offset: $this->offset,
                limit: $this->limit,
                rankingProfile: $this->rankingProfile,
            );
        } else {
            $raw = $service->vespaClient->textSearch(
                query: $this->query,
                docType: 'product',
                fields: ['title', 'description'],
                defaultIndex: 'description',
                grammar: $this->grammar,
                modelType: $this->modelType,
                documentSummary: 'main',
                limit: $this->limit,
                offset: $this->offset,
            );
        }

        $this->results = array_map(
            static fn (array $hit): array => [
                'title' => $hit['fields']['title'] ?? null,
                'description' => $hit['fields']['description'] ?? null,
                'category' => $hit['fields']['category'] ?? null,
                'priceUnitAmount' => $hit['fields']['priceUnitAmount'] ?? null,
            ],
            $raw['root']['children'],
        );

        $this->results = $raw;
    }

    /** @return array<array-key, mixed> */
    public function getResults(): array
    {
        return $this->results;
    }
}
