<?php

namespace App\Tests\Api;

use App\Entity\Price;
use App\Entity\User;
use App\Entity\UserCar;
use App\Factory\MediaFactory;
use App\Factory\UserFactory;
use App\Tests\WebAntTestCase;

class UserCarTest extends WebAntTestCase
{
    public function testGetCollection(): void
    {
        self::bootKernel();

        UserFactory::createMany(50);

        $response = self::createClient()->request('GET', '/api/user_cars');

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/UserCar',
            '@id' => '/api/user_cars',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 50,
            'hydra:view' => [
                '@id' => '/api/user_cars?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/user_cars?page=1',
                'hydra:last' => '/api/user_cars?page=10',
                'hydra:next' => '/api/user_cars?page=2',
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(UserCar::class);
    }

    public function testGetItem(): void
    {
        self::bootKernel();
        $object = UserFactory::createOne();
        $id = $object->getId();

        $userCar = static::getContainer()->get('doctrine')->
        getRepository(UserCar::class)->
        findOneBy(['user' => $id]);



        $iri=$this->findIriBy(User::class,['id' => $id]);


        static::createClient()->request('GET', '/api/user_cars/' . $userCar->id);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/UserCar',
            '@id' => '/api/user_cars/' . $userCar->getId(),
            '@type' => 'UserCar',
            'user' => $iri
        ]);
    }

}