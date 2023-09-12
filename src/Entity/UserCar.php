<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use App\Repository\UsersCarsRepository;
use App\Repository\UsersItemsInTheCarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UsersCarsRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('" . self::ROLE_ADMIN . "')" ),
        new Get(
            uriTemplate: '/users/{id}/car',
            uriVariables: [
                'id' => new Link(
                    fromProperty: 'car',
                    fromClass: User::class
                )
            ],
            normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'GetUsersItemsInTheCar']],
            denormalizationContext: ['groups' => ['SetUser']],
            security: "is_granted('" . self::ROLE_USER . "')",
        )
    ],
    normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
    denormalizationContext: ['groups' => ['SetCar']],
)]
class UserCar extends BaseEntity
{
    public const S_GROUP_GET_ONE = 'GetCar';
    public const S_GROUP_GET_MANY = 'GetCarObj';
    public function __construct()
    {
        parent::__construct();

        $this->itemsInTheCar=new ArrayCollection();
        $this->addItemsInTheCar(new UsersItemsInTheCar());

    }

    #[ORM\OneToOne(inversedBy: 'car', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id',referencedColumnName: 'id')]
    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY, 'SetCar'])]
    public User $user;

    #[Groups(['GetUsersItemsInTheCar'])]
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: UsersItemsInTheCar::class,cascade: ['persist','remove'])]
    public iterable $itemsInTheCar;


    public function getItemsInTheCar(): iterable
    {
        return $this->itemsInTheCar;
    }

    public function addItemsInTheCar(UsersItemsInTheCar $itemsInTheCar): self
    {
        if (!$this->itemsInTheCar->contains($itemsInTheCar)) {
            $this->itemsInTheCar->add($itemsInTheCar);
            $itemsInTheCar->setCar($this);
        }

        return $this;
    }

    public function removeItemsInTheCar(UsersItemsInTheCar $itemsInTheCar): self
    {
        if ($this->itemsInTheCar->removeElement($itemsInTheCar)) {
            // set the owning side to null (unless already changed)
            if ($itemsInTheCar->getCar() === $this) {
                $itemsInTheCar->setCar(null);
            }
        }

        return $this;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


}