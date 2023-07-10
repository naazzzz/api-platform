<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\EventListener\EntityListener;
use App\Repository\UserRepository;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource
(
    operations: [
        new Get(
            normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
            denormalizationContext: ['groups' => ['SetUser',]],
            security: "is_granted('" . self::ROLE_ADMIN . "') or is_granted('" . self::ROLE_USER . "')"
        ),
        new Post(
            normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
            denormalizationContext: ['groups' => ['SetUser']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY,]],
            denormalizationContext: ['groups' => ['SetUser']],
            security: "is_granted('" . self::ROLE_ADMIN . "') or is_granted('" . self::ROLE_USER . "')"
        ),
        new Put(
            normalizationContext: ['groups' => [self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY]],
            denormalizationContext: ['groups' => ['SetUser']],
            security: "is_granted('" . self::ROLE_ADMIN . "') or is_granted('" . self::ROLE_USER . "')"
        ),
        new Delete(
            security: "is_granted('" . self::ROLE_ADMIN . "')"
        ),

    ],

    paginationEnabled: true,

)
]
#[UniqueEntity("email", 'Пользователь с таким email уже существует')]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const S_GROUP_GET_ONE = 'GetUser';
    public const S_GROUP_GET_MANY = 'GetUserObj';


    #[Groups([self::S_GROUP_GET_ONE, self::S_GROUP_GET_MANY,'SetUser'])]
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    public ?string $email = null;

    #[Groups(['GetUserFromAdmin','SetUserFromAdmin'])]
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[SerializedName('password')]
    #[Assert\Length(min: 5, minMessage: "Short password")]
    #[Groups('SetUser')]
    private ?string $plainPassword = null;


    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserCar::class, cascade: ['persist'])]
    public UserCar $car;

    /**
     * @return UserCar
     */
    public function getUsersCars(): UserCar
    {
        return $this->car;
    }

    /**
     * @param UserCar $car
     */
    public function setUsersCars(UserCar $car): void
    {
        $this->car = $car;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


}
