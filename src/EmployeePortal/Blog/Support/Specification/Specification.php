<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support\Specification;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Expression;
use Webmozart\Assert\Assert;

use function array_map;

final readonly class Specification
{
    public function __construct(
        /** @var list<Criteria> */
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
            static fn (Criteria $criteria): Expression => $criteria->getWhereExpression(), // @phpstan-ignore return.type
            $this->criteria,
        );

        return Criteria::create()->where(new CompositeExpression(
            CompositeExpression::TYPE_AND,
            $expressions,
        ));
    }
}
