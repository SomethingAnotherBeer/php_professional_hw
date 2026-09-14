<?php
declare(strict_types=1);
namespace App\ValueObject\Connection;

class RedisConnectionValue
{
    protected string $redis_host;

    protected int $redis_port;

    public function __construct(array $params)
    {
        $this->redis_host = $params['redis_host'];
        $this->redis_port = $params['redis_port'];
    }

    public function getRedisHost(): string
    {
        return $this->redis_host;
    }

    public function getRedisPort(): int
    {
        return $this->redis_port;
    }

}