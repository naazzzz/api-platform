<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => [self::ITEM, self::ITEM_READ]],
    denormalizationContext: ['groups' => [self::ITEM, self::ITEM_WRITE,]]
)]
class Product extends BaseEntity
{

    public function __construct()
    {
        parent::__construct();

        $this->itemsInTheCar=new ArrayCollection();

    }
    #[Groups(self::ITEM)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(self::ITEM)]
    #[ORM\Column(type: 'integer', length: 255)]
    private ?int $amount = null;

    #[Groups(self::ITEM)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToOne(targetEntity: Price::class, inversedBy: 'products')]
    private ?Price $price = null;

//    #[Groups(self::ITEM_READ)]
    #[ORM\ManyToMany(targetEntity: UsersItemsInTheCar::class, mappedBy: 'products')]
    public iterable $itemsInTheCar;


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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
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
