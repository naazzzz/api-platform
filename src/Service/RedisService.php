<?php

namespace App\Service;

use App\Helper\RedisHelper;
use RedisException;
use Symfony\Component\HttpFoundation\Response;

class RedisService
{
    public function __construct(public RedisHelper $redisHelper)
    {

    }

    public function setAction($key, $value, $ttl = null)
    {
        $result = null;

        try {
            if ($key && $value) {
                $this->redisHelper->set($key, $value, $ttl);
                $result = ['key' => $key, 'value' => $value, 'ttl' => $ttl];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }


    public function getAction($key)
    {
        $result = null;

        try {
            if ($key) {
                $result = ['key' => $key, 'value' => $this->redisHelper->get($key)];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }


    public function ttlAction($key)
    {
        $result = null;

        try {
            if ($key) {
                $result = ['key' => $key, 'ttl' => $this->redisHelper->getTtl($key)];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }


    public function persistAction($key)
    {
        $result = null;

        try {
            if ($key) {
                $result = ['key' => $key, 'persist' => $this->redisHelper->persist($key)];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }


    public function expireAction($key, $ttl)
    {
        $result = null;

        try {
            if ($key) {
                $result = ['key' => $key, 'expire' => $this->redisHelper->expire($key, $ttl)];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }


    public function deleteAction($key)
    {
        $result = null;

        try {
            if ($key) {
                $result = ['key' => $key, 'expire' => $this->redisHelper->delete($key)];
            }
        } catch (RedisException $e) {
            $result = $e->getMessage();
        }

        return new Response(json_encode($result));
    }

}