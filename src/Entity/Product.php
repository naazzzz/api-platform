<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
    denormalizationContext: ['groups' => ['SetProduct']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class,
    properties: [
        'name' => SearchFilterInterface::STRATEGY_PARTIAL,
        'category' => SearchFilterInterface::STRATEGY_EXACT
        ])]
class Product extends BaseEntity
{
    public const PRODUCT_CATEGORY_WATER = 0;
    public const PRODUCT_CATEGORY_MEAT = 1;
    public const PRODUCT_CATEGORY_MILK = 2;
    public const PRODUCT_CATEGORY_SUGAR = 3;
    public const PRODUCT_CATEGORY_BREAD = 4;

    public const PRODUCTS_CATEGORY_ARRAY = [
        self::PRODUCT_CATEGORY_BREAD,
        self::PRODUCT_CATEGORY_MEAT,
        self::PRODUCT_CATEGORY_SUGAR,
        self::PRODUCT_CATEGORY_WATER,
        self::PRODUCT_CATEGORY_MILK
    ];

    public const S_GROUP_GET_ONE = 'GetProduct';
    public const S_GROUP_GET_MANY = 'GetProductObj';

    public function __construct()
    {
        parent::__construct();

        $this->itemsInTheCar = new ArrayCollection();

    }

    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetProduct'])]
    #[ORM\Column]
    private ?int $category = null;

    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetProduct'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetProduct'])]
    #[ORM\Column]
    private ?int $amount = null;

    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetProduct'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToOne(targetEntity: Price::class, inversedBy: 'products')]
    private ?Price $price = null;

    #[ORM\ManyToMany(targetEntity: UsersItemsInTheCar::class, mappedBy: 'products')]
    public iterable $itemsInTheCar;

    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetProduct'])]
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(types: ['https://schema.org/image'])]
    public ?Media $image = null;

    public function getItemsInTheCar(): iterable
    {
        return $this->itemsInTheCar;
    }

    public function addItemsInTheCar(UsersItemsInTheCar $itemsInTheCar): self
    {
        if (!$this->itemsInTheCar->contains($itemsInTheCar)) {
            $this->itemsInTheCar->add($itemsInTheCar);
            $itemsInTheCar->addProducts($this);
        }

        return $this;
    }

    public function removeItemsInTheCar(UsersItemsInTheCar $itemsInTheCar): self
    {
        if ($this->itemsInTheCar->removeElement($itemsInTheCar)) {
            // set the owning side to null (unless already changed)
            $itemsInTheCar->removeProducts($this);
        }

        return $this;
    }


    public function getCategory(): ?int
    {
        return $this->category;
    }


    public function setCategory(?int $category): void
    {
        $this->category = $category;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }


    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): static
    {
        $this->price = $price;

        return $this;
    }


}
