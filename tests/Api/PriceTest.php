<?php

namespace App\Tests\Api;

use App\Entity\Price;
use App\Factory\PriceFactory;
use App\Tests\WebAntTestCase;

class PriceTest extends WebAntTestCase
{
    public function testGetCollection(): void
    {
        self::bootKernel();

        PriceFactory::createMany(200);
        $response = static::createClient()->request('GET', '/api/prices');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Price',
            '@id' => '/api/prices',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 200
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Price::class);
    }

    public function testGetCollectionWithCustomFillters(): void
    {
        self::bootKernel();

        PriceFactory::createOne([
            'price' => 25,
        ]);
        PriceFactory::createOne([
            'price' => 2500,
        ]);
        PriceFactory::createOne([
            'price' => 250,
        ]);

        $response = static::createClient()->request('GET', '/api/prices?minValue=100&maxValue=1000');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/Price",
            "@id" => "/api/prices",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 1,
        ]);
        $this->assertCount(1, $response->toArray()['hydra:member']);
    }

    public function testCreatePrice(): void
    {
        self::bootKernel();

        static::createClient()->request('POST', '/api/prices',
            [
                'json' => [
                    'price' => 2
                ]
            ]
        );
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            "@context" => "/api/contexts/Price",
            "@id" => "/api/prices/1",
            "@type" => "Price",
            "price" => 2
        ]);
    }

    public function testGetItem(): void
    {
        self::bootKernel();

        $object = PriceFactory::createOne();
        $price = $object->getPrice();
        $id = $object->getId();

        static::createClient()->request('GET', '/api/prices/' . $id);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/Price",
            "@id" => "/api/prices/" . $id,
            "@type" => "Price",
            "price" => $price
        ]);
    }

    public function testPutItem(): void
    {
        self::bootKernel();
        $object = PriceFactory::createOne();
        $id = $object->getId();
        $newPrice=100000;

        static::createClient()->request('PUT', '/api/prices/' . $id,[
            'json' => [
                'price' => $newPrice
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/Price",
            "@id" => "/api/prices/" . $id,
            "@type" => "Price",
            "price" => $newPrice
        ]);
    }

    public function testPatchItem(): void
    {
        self::bootKernel();
        $object = PriceFactory::createOne();
        $id = $object->getId();
        $newPrice=100000;



        static::createClient()->request('PATCH', '/api/prices/' . $id,
            [
            'json' => [
                'price' => $newPrice
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/Price",
            "@id" => "/api/prices/" . $id,
            "@type" => "Price",
            "price" => $newPrice
        ]);
    }

    public function testDeleteItem(): void
    {
        self::bootKernel();
        $object = PriceFactory::createOne();
        $id = $object->getId();

        static::createClient()->request('DELETE', '/api/prices/' . $id);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->
            getRepository(Price::class)->
            findOneBy(['id' => $id]));
    }

}