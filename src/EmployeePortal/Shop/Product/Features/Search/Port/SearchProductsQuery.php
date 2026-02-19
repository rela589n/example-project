<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Search\Port;

final class SearchProductsQuery
{
    /** @var list<array<string, mixed>> */
    private array $results = [];

    public function __construct(
        private readonly string $query,
        private readonly int $offset = 0,
        private readonly int $limit = 10,
    ) {
    }

    public function process(SearchProductsService $service): void
    {
        $raw = $service->vespaClient->search(
            query: $this->query,
            docType: 'product',
            fields: ['title', 'description'],
            defaultIndex: 'description',
            limit: $this->limit,
            offset: $this->offset,
        );

        /** @var array{children?: list<array{id:string,fields:array<string,mixed>}>} $root */
        $root = $raw['root'] ?? [];

        $hits = $root['children'] ?? [];

        $this->results = array_map(
            static fn (array $hit): array => $hit['fields'],
            $hits,
        );
    }

    /** @return list<array<string, mixed>> */
    public function getResults(): array
    {
        return $this->results;
    }
}
