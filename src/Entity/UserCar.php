<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use App\Repository\UsersCarsRepository;
use App\Repository\UsersItemsInTheCarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: UsersCarsRepository::class)]
#[ApiResource]
class UserCar extends BaseEntity
{

    public function __construct()
    {
        parent::__construct();

        $this->itemsInTheCar=new ArrayCollection();
        $this->addItemsInTheCar(new UsersItemsInTheCar());

    }



    #[ORM\OneToOne(inversedBy: 'car', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id',referencedColumnName: 'id')]
    public User $user;

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