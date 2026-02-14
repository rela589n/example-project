<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product;

use App\EmployeePortal\Shop\Category\Category;
use App\EmployeePortal\Shop\Product\Description\Description;
use App\EmployeePortal\Shop\Product\Features\Create\ProductCreatedEvent;
use App\EmployeePortal\Shop\Product\Price\Price;
use App\EmployeePortal\Shop\Product\Title\Title;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    #[ORM\Embedded(columnPrefix: false)]
    private(set) Title $title;

    #[ORM\Embedded(columnPrefix: false)]
    private(set) Description $description;

    #[ORM\Embedded]
    private(set) Price $price;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private(set) Category $category;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private(set) CarbonImmutable $updatedAt;

    public function __construct(ProductCreatedEvent $event)
    {
        $this->id = $event->id;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->price = $event->price;
        $this->category = $event->category;
        $this->createdAt = $event->timestamp;
        $this->updatedAt = $event->timestamp;
    }
}
