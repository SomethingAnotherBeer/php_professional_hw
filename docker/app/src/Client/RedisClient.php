<?php
declare(strict_types=1);
namespace App\Client;

use App\ValueObject\Connection\RedisConnectionValue;

class RedisClient
{
    protected static ?RedisClient $redisClient = null;
    
    protected \Redis $redisConnection;

    public static function makeRedisClient(RedisConnectionValue $redisConnectionValue): RedisClient
    {
        if (!static::$redisClient) {
            static::$redisClient = new RedisClient($redisConnectionValue);
        }

        return static::$redisClient;
    }

    protected function __construct(RedisConnectionValue $redisConnectionValue)
    {
        $this->redisConnection = new \Redis();
        $this->redisConnection->set($redisConnectionValue->getRedisHost(), $redisConnectionValue->getRedisPort());

    }

    public function getConnection(): \Redis
    {
        return $this->redisConnection;
    }

}