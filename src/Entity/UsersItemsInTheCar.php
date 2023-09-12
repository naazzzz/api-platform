<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UsersItemsInTheCarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UsersItemsInTheCarRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
    denormalizationContext: ['groups' => ['SetItems']],
    paginationItemsPerPage: 10
)]
class UsersItemsInTheCar extends BaseEntity
{
    public const S_GROUP_GET_ONE = 'GetItems';
    public const S_GROUP_GET_MANY = 'GetItemsObj';
    public function __construct()
    {
        parent::__construct();

        $this->products=new ArrayCollection();

    }

    #[ApiProperty(readableLink: false, writableLink: false)]
    #[ORM\ManyToOne(targetEntity: UserCar::class, cascade: ['persist','remove'], inversedBy: 'itemsInTheCar')]
    #[ORM\JoinColumn(name: 'car_id',referencedColumnName: 'id')]
    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetItems'])]
    public UserCar $car;


    #[ApiProperty(readableLink: true, writableLink: false)]
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'itemsInTheCar',cascade: ['persist'])]
    #[ORM\JoinTable(name:'products')]
    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetItems','GetUsersItemsInTheCar'])]
    public iterable $products;

    /**
     * @return UserCar
     */
    public function getCar(): UserCar
    {
        return $this->car;
    }

    /**
     * @param UserCar $car
     */
    public function setCar(UserCar $car): void
    {
        $this->car = $car;
    }

    public function getProducts(): iterable
    {
        return $this->products;
    }

    public function addProducts(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addItemsInTheCar($this);
        }

        return $this;
    }

    public function removeProducts(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            $product->removeItemsInTheCar($this);
        }

        return $this;
    }






}