<?php
declare(strict_types=1);
namespace App\Factory;

class MemcachedFactory
{
    private static ?\Memcached $memcached = null;


    public static function makeInstance(): \Memcached
    {   
        if (null === static::$memcached) {
            $memcached = new \Memcached();
            $memcached->addServer($_ENV['MEMCACHED_HOST'], (int)$_ENV['MEMCACHED_PORT']);

            static::$memcached = $memcached;
        }

        return static::$memcached;
    }

}