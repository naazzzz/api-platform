<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;

abstract class BaseEntity
{
    public const ITEM = 'ITEM';
    public const ITEM_READ = 'ITEM:write';
    public const ITEM_WRITE = 'ITEM:read';

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct()
    {
        $this->setDateCreate(new \DateTime());
        $this->setDateUpdate(new \DateTime());
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $dateCreate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $dateUpdate;

    /**
     * @param \DateTimeInterface|null $dateCreate
     * @param \DateTimeInterface|null $dateUpdate
     */

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    /**
     * @param \DateTimeInterface|null $dateCreate
     */
    public function setDateCreate(?\DateTimeInterface $dateCreate): void
    {
        $this->dateCreate = $dateCreate;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    /**
     * @param \DateTimeInterface|null $dateUpdate
     */
    public function setDateUpdate(?\DateTimeInterface $dateUpdate): void
    {
        $this->dateUpdate = $dateUpdate;
    }



}