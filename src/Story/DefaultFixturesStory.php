<?php

namespace App\Story;

use App\Factory\AccessTokenFactory;
use App\Factory\ClientFactory;
use App\Factory\MediaFactory;
use App\Factory\PriceFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultFixturesStory extends Story
{
    public function build(): void
    {


        $userAdmin = UserFactory::createOne([
            'email' => 'naazAdmin@webant.com',
            'password' => '1234567',
            'roles' => ['ROLE_ADMIN']
        ]);


        $client = ClientFactory::createOne([
            'identifier' => '123',
            'secret'=>'123',
        ]);

        AccessTokenFactory::createOne([
            'identifier' => 'admin',
            'client' => $client,
            'expiry' => new \DateTimeImmutable('+1 hour'),
            'user_identifier' => 'naazAdmin@webant.com',
            'scopes' => [],
        ]);

        MediaFactory::createMany(5);
        PriceFactory::createMany(200);
        ProductFactory::createMany(200);
        UserFactory::createMany(50);
    }
}
