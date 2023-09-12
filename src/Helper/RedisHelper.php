<?php

namespace App\Helper;

use Redis;

class RedisHelper
{
    const MIN_TTL = 1;
    const MAX_TTL = 3600;

    public function __construct()
    {
        $this->host = $_ENV['REDIS_HOST'];
        $this->port = $_ENV['REDIS_PORT'];
    }

    /** @var Redis $redis */
    private $redis;

    private $host;

    private $port;

    private function connect()
    {
        if (!$this->redis || $this->redis->ping() != '+PONG') {
            $this->redis = new Redis();

            $this->redis->connect($this->host, $this->port);
        }
    }

    public function get($key)
    {
        $this->connect();

        return $this->redis->get($key);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->connect();

        if (is_null($ttl)) {
            $this->redis->set($key, $value);
        } else {
            $this->redis->setex($key, $this->normaliseTtl($ttl), $value);
        }
    }

    public function expire($key, $ttl = self::MIN_TTL)
    {
        $this->connect();

        return $this->redis->expire($key, $this->normaliseTtl($ttl));
    }

    public function delete($key)
    {
        $this->connect();

        return $this->redis->del($key);
    }

    public function getTtl($key)
    {
        $this->connect();

        return $this->redis->ttl($key);
    }

    public function persist($key)
    {
        $this->connect();

        return $this->redis->persist($key);
    }

    private function normaliseTtl($ttl)
    {
        $ttl = ceil(abs($ttl));

        return ($ttl >= self::MIN_TTL && $ttl <= self::MAX_TTL) ? $ttl : self::MAX_TTL;
    }


}