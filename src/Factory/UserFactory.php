<?php

namespace App\Factory;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserCar;
use App\Repository\ProductRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Product>
 *
 * @method        Product|Proxy                     create(array|callable $attributes = [])
 * @method static Product|Proxy                     createOne(array $attributes = [])
 * @method static Product|Proxy                     find(object|array|mixed $criteria)
 * @method static Product|Proxy                     findOrCreate(array $attributes)
 * @method static Product|Proxy                     first(string $sortedField = 'id')
 * @method static Product|Proxy                     last(string $sortedField = 'id')
 * @method static Product|Proxy                     random(array $attributes = [])
 * @method static Product|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ProductRepository|RepositoryProxy repository()
 * @method static Product[]|Proxy[]                 all()
 * @method static Product[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Product[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Product[]|Proxy[]                 findBy(array $attributes)
 * @method static Product[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Product[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    )
    {
        parent::__construct();
    }


    protected static function getClass(): string
    {
        return User::class;
    }

    protected function getDefaults(): array
    {
    return [
        'email'=> self::faker()->email(),
        'password'=> self::faker()->password(),
        'dateCreate' => self::faker()->dateTime(),
        'dateUpdate' => self::faker()->dateTime(),
    ];

    }

    protected function initialize(): self
    {
        return $this
             ->afterInstantiate(function(User $user): void {
                     $car = new UserCar();
                     $car->setUser($user);
                     $user->setUsersCars($car);
                     $user->setPassword($this->passwordEncoder->hashPassword($user,$user->getPassword()));
             })
            ;
    }
}