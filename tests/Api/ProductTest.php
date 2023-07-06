<?php

namespace App\Tests\Api;

use App\Entity\Media;
use App\Entity\Price;
use App\Entity\Product;
use App\Factory\MediaFactory;
use App\Factory\PriceFactory;
use App\Factory\ProductFactory;
use App\Tests\WebAntTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductTest extends WebAntTestCase
{
    public function testGetCollection(): void
    {
        self::startUp();

        $response = static::createClient()->request(Request::METHOD_GET, '/api/products');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 200,
            'hydra:view' => [
                '@id' => '/api/products?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last' => '/api/products?page=20',
                'hydra:next' => '/api/products?page=2',
            ],
        ]);
        $this->assertCount(10, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }

    public function testCreateProduct(): void
    {
        self::bootKernel();

        $price = PriceFactory::createOne()->getPrice();
        $imageId = MediaFactory::createOne()->getId();

        $iriPrice = $this->findIriBy(Price::class, ['price' => $price]);
        $iriImage = $this->findIriBy(Media::class, ['id' => $imageId]);

        static::createClient()->request('POST', '/api/products', [
                'json' => [
                    "category" => 0,
                    "name" => "testProduct",
                    "amount" => 1,
                    'price' => $iriPrice,
                    'image' => $iriImage
                ]
            ]
        );
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@id" => "/api/products/1",
            "@type" => "Product"
        ]);
    }

    public function testGetItem(): void
    {
        self::bootKernel();

        $price = PriceFactory::createOne();
        $image = MediaFactory::createOne();


        $iriPrice = $this->findIriBy(Price::class, ['price' => $price->getPrice()]);
        $iriImage = $this->findIriBy(Media::class, ['id' => $image->getId()]);

        $product = ProductFactory::createOne(
            [
                'price' => $price,
                'image' => $image
            ])
            ->enableAutoRefresh();

        static::createClient()->request('GET', '/api/products/' . $product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@id" => "/api/products/" . $product->refresh()->getId(),
            "@type" => "Product",
            'category' => $product->refresh()->getCategory(),
            'name' => $product->refresh()->getName(),
            'amount' => $product->refresh()->getAmount(),
            "price" => $iriPrice,
            'image' => $iriImage
        ]);
    }

    public function testPutItem(): void
    {
        self::bootKernel();
        $newPrice = PriceFactory::createOne();
        $image = MediaFactory::createOne();
        $object = ProductFactory::createOne();
        $id = $object->getId();

        $iriPrice = $this->findIriBy(Price::class, ['price' => $newPrice->getPrice()]);
        $iriImage = $this->findIriBy(Media::class, ['id' => $image->getId()]);

        static::createClient()->request('PUT', '/api/products/' . $id, [
            'json' => [
                'price' => $iriPrice,
                'name' => 'bla-bla-test',
                'amount' => 2,
                'category' => 3,
                'image' => $iriImage
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@id" => "/api/products/" . $id,
            "@type" => "Product",
            'price' => $iriPrice,
            'name' => 'bla-bla-test',
            'amount' => 2,
            'category' => 3,
            'image' => $iriImage
        ]);
    }
    public function testPatchItem(): void
    {
        self::bootKernel();
        $newPrice = PriceFactory::createOne();
        $image = MediaFactory::createOne();
        $object = ProductFactory::createOne();
        $id = $object->getId();

        $iriPrice = $this->findIriBy(Price::class, ['price' => $newPrice->getPrice()]);
        $iriImage = $this->findIriBy(Media::class, ['id' => $image->getId()]);

        static::createClient()->request('PATCH', '/api/products/' . $id, [
            'json' => [
                'amount' => 0,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@id" => "/api/products/" . $id,
            "@type" => "Product",
            'price' => $iriPrice,
            'amount' => 0,
            'image' => $iriImage
        ]);
    }

    public function testDeleteItem(): void
    {
        self::bootKernel();
        $newPrice = PriceFactory::createOne();
        $image = MediaFactory::createOne();
        $object = ProductFactory::createOne();
        $id = $object->getId();

        static::createClient()->request('DELETE', '/api/products/' . $id);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->
            getRepository(Product::class)->
            findOneBy(['id' => $id]));
    }
}