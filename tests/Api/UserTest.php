<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Tests\WebAntTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserTest extends WebAntTestCase
{

    public function testGetCollection(): void
    {
        self::startUp();

        $response = static::createClientWithCredentials()->request(Request::METHOD_GET, '/api/users');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 51,
            'hydra:view' => [
                '@id' => '/api/users?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/users?page=1',
                'hydra:last' => '/api/users?page=11',
                'hydra:next' => '/api/users?page=2',
            ],
        ]);
        $this->assertCount(5, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(User::class);

    }

    public function testCreateUser(): void
    {

        static::createClient()->request('POST', '/api/users',
            ['json' => [
                'email' => 'naaz@webant.com',
                'password' => '1234567'
            ]]
        );

        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8',
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users/1',
            '@type' => 'User',
            'email' => 'naaz@webant.com'
        ]);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testCreateInvalidUser(): void
    {
        static::createClient()->request('POST', '/api/users',
            ['json' => [
                'email' => 'nnaaz',
                'password' => '1234567'
            ]]);

        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8',
        );

        $this->assertResponseStatusCodeSame(422);

        $this->assertJsonContains(
            [
                "@context" => "/api/contexts/ConstraintViolationList",
                "@type" => "ConstraintViolationList",
                "hydra:title" => "An error occurred",
                "hydra:description" => "email: This value is not a valid email address.",
            ]
        );

    }

}