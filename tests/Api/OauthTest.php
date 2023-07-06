<?php

namespace App\Tests\Api;

use App\Tests\WebAntTestCase;
use function Symfony\Component\String\b;

class OauthTest extends WebAntTestCase
{
    function testGetAccessToken(): void
    {
        self::startUp();

        $options['extra']['parameters'] = [
            'client_id'=>'123',
            'client_secret' => '123',
            'grant_type' => 'password',
            'username' => 'naazAdmin@webant.com',
            'password' => '1234567',
        ];
        $client = static::createClient();
        $response = $client->request('POST', '/token', $options);

        $this->assertResponseIsSuccessful();

    }

//    function testFailLogin(): void
//    {
//        $options['extra']['parameters'] = [
//            'client_id'=>'1_123',
//            'client_secret' => '123',
//            'grant_type' => 'password',
//            'username' => '70000000001',
//            'password' => 'pass',
//        ];
//
//        $response = static::createClient()->request('POST', '/oauth/v2/token', $options);
//
//        $this->assertResponseStatusCodeSame(400);
//    }


}