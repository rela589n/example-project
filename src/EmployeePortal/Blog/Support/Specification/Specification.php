<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support\Specification;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;

final readonly class Specification
{
    public function __construct(
        /** @var Criteria[] */
        private array $criteria,
    ) {
    }

    public function with(Criteria $criteria): self
    {
        return new self([...$this->criteria, $criteria]);
    }

    public function getCriteria(): Criteria
    {
        $expressions = array_map(
            static fn (Criteria $criteria) => $criteria->getWhereExpression(),
            $this->criteria,
        );

        return Criteria::create()->where(new CompositeExpression(
            CompositeExpression::TYPE_AND,
            $expressions,
        ));
    }
}
