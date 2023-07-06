<?php

namespace App\Tests\Api;

use App\Entity\Media;
use App\Factory\MediaFactory;
use App\Tests\WebAntTestCase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaTest extends WebAntTestCase
{
    public function testUploadFile(): void
    {
        self::bootKernel();

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('default.storage');

        $file = new UploadedFile('./tests/Fixtures/files/photo.jpg', 'file.jpg');

        $client = static::createClient()->request('POST', '/api/media', [
            'headers' => [
                'accept' => 'application/json'
            ],
            'extra' => [
                'files' => [
                    'file' => $file,
                ]
            ]
        ]);

        $this->assertResponseIsSuccessful();

        $fileName = explode('/media/', json_decode($client->getContent(), true)['contentUrl']);
        $this->assertTrue($storage->fileExists($fileName[1]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetCollection(): void
    {
        self::bootKernel();

        MediaFactory::createMany(15);

        $response = static::createClient()->request('GET', '/api/media');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "@context" => "/api/contexts/Media",
            "@id" => "/api/media",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 15,
        ]);
        $this->assertCount(5,$response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Media::class);
    }

    public function testGetItem():void
    {
        self::bootKernel();
        $object= MediaFactory::createOne();
        $id=$object->getId();
        $path=$object->filePath;
        $response=static::createClient()->request('GET','/api/media/'.$id);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Media',
            '@id' => '/api/media/'.$id,
            '@type' => 'https://schema.org/MediaObject',
            'contentUrl' => '/media/'.$path
        ]);
    }

    public function testFileFormat(){
        self::bootKernel();


        $file = new UploadedFile('./tests/Fixtures/files/file.ico', 'file.ico');

        static::createClient()->request('POST', '/api/media', [
            'headers' => [
                'accept' => 'application/json'
            ],
            'extra' => [
                'files' => [
                    $file
                ]
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);

    }
}