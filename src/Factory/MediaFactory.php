<?php

namespace App\Factory;

use App\Entity\Media;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;
use function Zenstruck\Foundry\lazy;

/**
 * @extends ModelFactory<Media>
 *
 * @method        Media|Proxy                      create(array|callable $attributes = [])
 * @method static Media|Proxy                      createOne(array $attributes = [])
 * @method static Media|Proxy                      find(object|array|mixed $criteria)
 * @method static Media|Proxy                      findOrCreate(array $attributes)
 * @method static Media|Proxy                      first(string $sortedField = 'id')
 * @method static Media|Proxy                      last(string $sortedField = 'id')
 * @method static Media|Proxy                      random(array $attributes = [])
 * @method static Media|Proxy                      randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static Media[]|Proxy[]                  all()
 * @method static Media[]|Proxy[]                  createMany(int $number, array|callable $attributes = [])
 * @method static Media[]|Proxy[]                  createSequence(iterable|callable $sequence)
 * @method static Media[]|Proxy[]                  findBy(array $attributes)
 * @method static Media[]|Proxy[]                  randomRange(int $min, int $max, array $attributes = [])
 * @method static Media[]|Proxy[]                  randomSet(int $number, array $attributes = [])
 */
final class MediaFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();
    }


    protected function getDefaults(): array
    {

        return [
            'filePath' => self::faker()->image(fullPath: false),
            'dateCreate' => self::faker()->dateTime(),
            'dateUpdate' => self::faker()->dateTime(),
        ];
    }


    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Media $media): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Media::class;
    }
}
