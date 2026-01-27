<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Category;

use App\EmployeePortal\Entity\Product\Product;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    #[ORM\Column(unique: true)]
    private(set) string $name;

    /** @var Collection<string,Product> */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category', indexBy: 'id')]
    private Collection $products;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $updatedAt;

    public function __construct(
        string $name,
        CarbonImmutable $timestamp,
    ) {
        $this->id = Uuid::v7();
        $this->name = $name;
        $this->products = new ArrayCollection();
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
    }
}
