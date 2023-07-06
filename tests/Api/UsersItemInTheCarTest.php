<?php

namespace App\Tests\Api;

use App\Entity\Price;
use App\Entity\Product;
use App\Entity\UserCar;
use App\Entity\UsersItemsInTheCar;
use App\Factory\MediaFactory;
use App\Factory\PriceFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Tests\WebAntTestCase;

class UsersItemInTheCarTest extends WebAntTestCase
{
    public function testGetCollection(): void
    {
        self::startUp();

        $response = static::createClient()->request('GET', '/api/users_items_in_the_cars');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/UsersItemsInTheCar',
            '@id' => '/api/users_items_in_the_cars',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 51,
            'hydra:view' => [
                '@id' => '/api/users_items_in_the_cars?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/users_items_in_the_cars?page=1',
                'hydra:last' => '/api/users_items_in_the_cars?page=6',
                'hydra:next' => '/api/users_items_in_the_cars?page=2',
            ],
        ]);
        $this->assertCount(10, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(UsersItemsInTheCar::class);
    }

    public function testCreateUsersItemInTheCar(): void
    {
        self::bootKernel();
        MediaFactory::createOne();
        PriceFactory::createOne();
        $user = UserFactory::createOne();
        $product = ProductFactory::createOne();

        $id = $user->getId();


        $iriCar = $this->findIriBy(UserCar::class, ['user' => $id]);
        $iriProduct = $this->findIriBy(Product::class, ['name' => $product->getName()]);


        static::createClient()->request('POST', '/api/users_items_in_the_cars',
            [
                'json' => [
                    'car' => $iriCar,
                    'products' => [$iriProduct]
                ]
            ]
        );
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            "@context" => "/api/contexts/UsersItemsInTheCar",
            "@id" => "/api/users_items_in_the_cars/2",
            "@type" => "UsersItemsInTheCar",
            'car' => $iriCar,
            'products' => [$iriProduct]
        ]);
    }

    public function testGetItem(): void
    {
        self::bootKernel();
        MediaFactory::createOne();
        PriceFactory::createOne();
        $user = UserFactory::createOne();
        $id = $user->getId();

        $userCar = static::getContainer()->get('doctrine')->
        getRepository(UserCar::class)->
        findOneBy(['user' => $id]);

        $userItems = static::getContainer()->get('doctrine')->
        getRepository(UsersItemsInTheCar::class)->
        findOneBy(['car' => $userCar->id]);

        $iriCar = $this->findIriBy(UserCar::class, ['user' => $id]);

        static::createClient()->request('GET', '/api/users_items_in_the_cars/' . $userItems->id);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/UsersItemsInTheCar",
            "@id" => "/api/users_items_in_the_cars/1",
            "@type" => "UsersItemsInTheCar",
            'car' => $iriCar,
            'products' => []
        ]);
    }

    public function testPutItems(): void
    {
        self::bootKernel();
        MediaFactory::createOne();
        PriceFactory::createOne();
        $product = ProductFactory::createOne();
        $user = UserFactory::createOne();
        $id = $user->getId();

        $userCar = static::getContainer()->get('doctrine')->
        getRepository(UserCar::class)->
        findOneBy(['user' => $id]);

        $userItems = static::getContainer()->get('doctrine')->
        getRepository(UsersItemsInTheCar::class)->
        findOneBy(['car' => $userCar->id]);

        $iriProduct = $this->findIriBy(Product::class, ['name' => $product->getName()]);

        static::createClient()->request('PUT', '/api/users_items_in_the_cars/' . $userItems->id,
            [
                'json' => [
                    "products" => [
                        $iriProduct
                    ]
                ]
            ]
        );

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/UsersItemsInTheCar",
            "@id" => "/api/users_items_in_the_cars/1",
            "@type" => "UsersItemsInTheCar",
            'products' => [$iriProduct]
        ]);

    }

    public function testPatchItems(): void
    {
        self::bootKernel();
        MediaFactory::createOne();
        PriceFactory::createOne();
        $product = ProductFactory::createOne();
        $user = UserFactory::createOne();
        $id = $user->getId();

        $userCar = static::getContainer()->get('doctrine')->
        getRepository(UserCar::class)->
        findOneBy(['user' => $id]);

        $userItems = static::getContainer()->get('doctrine')->
        getRepository(UsersItemsInTheCar::class)->
        findOneBy(['car' => $userCar->id]);

        $iriProduct = $this->findIriBy(Product::class, ['name' => $product->getName()]);

        static::createClient()->request('PATCH', '/api/users_items_in_the_cars/' . $userItems->id,
            [
                'json' => [
                    "products" => [
                        $iriProduct
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ]
        );

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/UsersItemsInTheCar",
            "@id" => "/api/users_items_in_the_cars/1",
            "@type" => "UsersItemsInTheCar",
            'products' => [$iriProduct]
        ]);
    }

    public function testDeleteItems():void
    {
        self::bootKernel();

        $user = UserFactory::createOne();
        $id = $user->getId();

        $userCar = static::getContainer()->get('doctrine')->
        getRepository(UserCar::class)->
        findOneBy(['user' => $id]);

        $userItems = static::getContainer()->get('doctrine')->
        getRepository(UsersItemsInTheCar::class)->
        findOneBy(['car' => $userCar->id]);

        static::createClient()->request('DELETE', '/api/users_items_in_the_cars/' . $userItems->id);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->
            getRepository(UsersItemsInTheCar::class)->
            findOneBy(['id' => $userItems->id]));

    }
}