<?php
declare(strict_types=1);
namespace App\IdentityMap;

use App\Interface\IdentityItemInterface;

class IdentityMap
{
    private array $storage = [];

    public static function makeInstance(): IdentityMap
    {
        return new IdentityMap();
    }


    public function get(string $identifier): IdentityItemInterface
    {

        return $this->storage[$identifier] ?? null;

    }

    public function has(string $identifier): bool
    {
        return isset($this->storage[$identifier]);
    }


    public function set(IdentityItemInterface $item): IdentityItemInterface
    {
        $key = $this->extractKey($item);

        if ($this->has($key)) {
            if (method_exists($this->storage[$key], 'replace')) {
                $this->storage[$key]->replace($item);
            }
        }

        else {
            $this->storage[$key] = $item;
        }

        return $item;
    }

    protected function extractKey(IdentityItemInterface $item): string
    {
        return $item::class . ':' . $item->getId();
    }



}