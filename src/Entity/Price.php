<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Filter\PriceFilter;
use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['GetPrice']],
    denormalizationContext: ['groups' => ['SetPrice']]
)]
#[ApiFilter(PriceFilter::class, properties: ['price'])]
class Price extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();

        $this->products = new ArrayCollection();
    }

    #[Groups(['SetPrice','GetPrice'])]
    #[ORM\Column]
    private ?int $price = null;

    #[ORM\OneToMany(mappedBy: 'price', targetEntity: Product::class)]
    private iterable $products;


    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setPrice($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPrice() === $this) {
                $product->setPrice(null);
            }
        }

        return $this;
    }



}
