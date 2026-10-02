<?php
declare(strict_types=1);
namespace App\Client;

use App\Model\Client\RedisClientValue;
use Redis;

class RedisClient
{
    protected static ?RedisClient $redisClient = null;

    private Redis $redis;

    public static function makeInstance(RedisClientValue $redisClientValue): RedisClient
    {       
        if (!static::$redisClient) {
            static::$redisClient = new RedisClient($redisClientValue);
        }
        return static::$redisClient;
    }

    protected function __construct(RedisClientValue $redisClientValue)
    {
        $this->redis = new Redis();
        $this->redis->connect($redisClientValue->getRedisHost(), $redisClientValue->getRedisPort());
        $this->redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
    }

    public function getConnection(): Redis
    {
        return $this->redis;
    }

}