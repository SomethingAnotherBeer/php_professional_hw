<?php
declare(strict_types=1);
namespace App\Model\Client;

use App\Exception\Client\Redis\RedisHostCannotBeEmptyException;
use App\Exception\Client\Redis\RedisHostNotSpecifiedException;
use App\Exception\Client\Redis\RedisPortCannotBeLessThanZeroException;
use App\Exception\Client\Redis\RedisPortNotSpecifiedException;

class RedisClientValue
{
    private string $redis_host;

    private int $redis_port;

    public function __construct(string $redis_host, int $redis_port)
    {
        $this->checkRedisHost($redis_host);
        $this->checkRedisPort($redis_port);

        $this->redis_host = $redis_host;
        $this->redis_port = $redis_port;
    }

    public function getRedisHost(): string
    {
        return $this->redis_host;
    }

    public function getRedisPort(): int
    {
        return $this->redis_port;
    }

    private function checkRedisHost(string $redis_host): void
    {
        if ('' === trim($redis_host)) {
            throw new RedisHostCannotBeEmptyException("Хост редиса не может быть пустым");
        }
    }

    private function checkRedisPort(int $redis_port): void
    {
        if ($redis_port < 0) {
            throw new RedisPortCannotBeLessThanZeroException("Порт редиса не может быть меньше нуля");
        }
    }

    public static function makeInstance(string $redis_host, int $redis_port): RedisClientValue
    {
        return new RedisClientValue($redis_host, $redis_port);
    }

    public static function makeInstanceFromArray(array $redis_params)
    {
        if (!isset($redis_params['REDIS_HOST']) || !is_string($redis_params['REDIS_HOST'])) {
            throw new RedisHostNotSpecifiedException("Не указан хост редиса");
        }

        if (!isset($redis_params['REDIS_PORT']) || !is_int($redis_params['REDIS_PORT'])) {
            throw new RedisPortNotSpecifiedException("Не указан порт редиса");
        }

        return static::makeInstance($redis_params['REDIS_HOST'], $redis_params['REDIS_PORT']);
    }

}