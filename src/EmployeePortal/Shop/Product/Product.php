<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product;

use App\EmployeePortal\Shop\Category\Category;
use App\EmployeePortal\Shop\Product\_Features\Create\ProductCreatedEvent;
use App\EmployeePortal\Shop\Product\Description\Description;
use App\EmployeePortal\Shop\Product\Price\Price;
use App\EmployeePortal\Shop\Product\Title\Title;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Validation;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private(set) Uuid $id;

    /** @see Title */
    #[ORM\Column(unique: true)]
    public string $title {
        set => $value |> Validation::createCallable(new Sequentially([new NotBlank(), new Length(min: 4, max: 255)]));
    }

    /** @see Description */
    #[ORM\Column(type: 'text')]
    public string $description {
        set => $value |> Validation::createCallable(new Sequentially([new NotBlank(), new Length(min: 10, max: 2000)]));
    }

    /** @see Price */
    #[ORM\Column]
    public int $priceUnitAmount {
        set => $value |> Validation::createCallable(new PositiveOrZero());
    }

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
        $this->priceUnitAmount = $event->priceUnitAmount;
        $this->category = $event->category;
        $this->createdAt = $event->timestamp;
        $this->updatedAt = $event->timestamp;
    }
}
