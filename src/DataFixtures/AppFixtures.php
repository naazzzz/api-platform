<?php

namespace App\DataFixtures;

use App\Story\DefaultFixturesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Bundle\OAuth2ServerBundle\Model\Client;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultFixturesStory::load();
    }
}
