<?php
declare(strict_types=1);
namespace App\Service;
use App\Factory\MemcachedFactory;

class MailCheckerCacheStorage
{
    const DOMAIN_LIST_KEY = "domain_list";

    private \Memcached $memcached;


    public static function makeInstance(): MailCheckerCacheStorage
    {
        return new MailCheckerCacheStorage(MemcachedFactory::makeInstance());
    }

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
        $this->clearUnactual();
    }

    public function add(string $domain, int $ttl = 3600): static
    {
        $domain_cached_list = $this->memcached->get(static::DOMAIN_LIST_KEY);
        if (!is_array($domain_cached_list)) {
            $domain_cached_list = [];
        }
      
        $domain_cached_list[$domain] =
        [
            'timestamp_of_create' => (new \DateTimeImmutable())->getTimestamp(),
            'ttl' => $ttl,
        ];

        $this->memcached->set(static::DOMAIN_LIST_KEY, $domain_cached_list);

        return $this;
    }

    public function get(string $key): array
    {   

        $domain_cached_list = $this->memcached->get(static::DOMAIN_LIST_KEY);
        return (array_key_exists($key, $domain_cached_list)) ? $domain_cached_list[$key] : null;

    }

    public function has(string $key): bool
    {
        $domain_cached_list = $this->memcached->get(static::DOMAIN_LIST_KEY);
        return (array_key_exists($key, $domain_cached_list)) ? true : false;
    }

    public function clearAll(): static
    {

        $this->memcached->set(static::DOMAIN_LIST_KEY, []);

        return $this;
    }

    public function getAll(): array
    {   
        return $this->memcached->get(static::DOMAIN_LIST_KEY);
    }

    public function getAllKeys(): array
    {   
        return array_keys($this->memcached->get(static::DOMAIN_LIST_KEY));
    }


    private function clearUnactual(): void
    {   
        
        $domain_cached_list = $this->memcached->get(static::DOMAIN_LIST_KEY);
        $actual_domain_cached_list = [];

        if (is_array($domain_cached_list)) {
            $current_timestamp = (new \DateTimeImmutable())->getTimestamp();
            $is_unactual = false;
            foreach ($domain_cached_list as $cached_domain_key => $cached_domain_key_params) {
                $is_unactual = false;
                if (!is_array($cached_domain_key_params) || ( is_array($cached_domain_key_params) && ( !array_key_exists('timestamp_of_create', $cached_domain_key_params) || !array_key_exists('ttl', $cached_domain_key_params)))) {
                    $is_unactual = true;
                }

                if (($current_timestamp - ($cached_domain_key_params['timestamp_of_create'] + $cached_domain_key_params['ttl'])) >= 0) {
                    $is_unactual = true;
                }

                if (!$is_unactual) {
                    $actual_domain_cached_list[$cached_domain_key] = $cached_domain_key_params;
                }
            }
        }


        $this->memcached->set(static::DOMAIN_LIST_KEY, $actual_domain_cached_list);
    }


}