<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PrePersist;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity
{
    public const S_GROUP_GET_BASE = 'GetBase';
    public const S_GROUP_GET_OBJ_BASE = 'GetObjBase';
    public const S_GROUP_GET_ID = 'GetId';

    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    public function __construct()
    {
        $this->setDateCreate(new \DateTime());
        $this->setDateUpdate(new \DateTime());
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::S_GROUP_GET_ID,self::S_GROUP_GET_BASE,self::S_GROUP_GET_OBJ_BASE])]
    public ?int $id = null;

    #[Groups([self::S_GROUP_GET_BASE,self::S_GROUP_GET_OBJ_BASE,'GetDateFromAdmin'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $dateCreate;

    #[Groups([self::S_GROUP_GET_BASE,self::S_GROUP_GET_OBJ_BASE,'GetDateFromAdmin'])]
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