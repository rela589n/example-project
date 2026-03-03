<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Search\Port;

use App\EmployeePortal\Shop\Product\_Features\Search\SearchType;
use function array_map;

final class SearchProductsQuery
{
    /** @var list<array<string, mixed>> */
    private array $results = [];

    public function __construct(
        private readonly string $query,
        private readonly SearchType $searchType,
        private readonly int $offset = 0,
        private readonly int $limit = 10,
        private readonly ?string $modelType = null,
        private readonly string $grammar = 'weakAnd',
    ) {
    }

    public function process(SearchProductsService $service): void
    {
        if ($this->searchType === SearchType::VECTOR) {
            $raw = $service->vespaClient->vectorSearch(
                query: $this->query,
                docType: 'product',
                fields: ['title', 'description'],
                offset: $this->offset,
                limit: $this->limit,
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

        $this->results = $raw;
    }

    /** @return list<array<string, mixed>> */
    public function getResults(): array
    {
        return $this->results;
    }

    public function withSearchType(SearchType $searchType): self
    {
        return clone($this, [
            "searchType" => $searchType
        ]);
    }
}
