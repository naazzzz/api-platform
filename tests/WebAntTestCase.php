<?php

namespace App\Tests;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Story\DefaultFixturesStory;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\AccessTokenManager;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Client as ClientEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Scope as ScopeEntity;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken as AccessTokenModel;
use League\OAuth2\Server\CryptKey;

class WebAntTestCase extends ApiTestCase implements BaseTestInterface
{
    use ResetDatabase;
    use Factories;

    public static function startUp(): void
    {
        self::bootKernel();

        DefaultFixturesStory::load();
    }

    public static function generateJwtToken(AccessTokenModel $accessToken): string
    {
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($accessToken->getClient()->getIdentifier());
        $clientEntity->setRedirectUri(array_map('strval', $accessToken->getClient()->getRedirectUris()));

        $accessTokenEntity = new AccessTokenEntity();
        $accessTokenEntity->setPrivateKey(new CryptKey($_ENV['OAUTH_PRIVATE_KEY'], $_ENV['OAUTH_PRIVATE_KEY_PASS'], false));
        $accessTokenEntity->setIdentifier(identifier: $accessToken->getIdentifier());
        $accessTokenEntity->setExpiryDateTime(dateTime: $accessToken->getExpiry());
        $accessTokenEntity->setClient($clientEntity);
        $accessTokenEntity->setUserIdentifier($accessToken->getUserIdentifier());

        foreach ($accessToken->getScopes() as $scope) {
            $scopeEntity = new ScopeEntity();
            $scopeEntity->setIdentifier((string) $scope);
            $accessTokenEntity->addScope($scopeEntity);
        }

        return (string) $accessTokenEntity;
    }

    public static function createClientWithCredentials(): Client
    {
        $accessManagers = static::getContainer()->get(AccessTokenManager::class);
        $accessToken = $accessManagers->find('admin');

        $token = self::generateJwtToken($accessToken);

        $defaultOptions = [
            'headers'=> array('Authorization'=>'Bearer '.$token),
        ];

        return static::createClient([], $defaultOptions);
    }

}
