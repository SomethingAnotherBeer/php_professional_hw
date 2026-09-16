<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class EventValue implements Iterator
{
    private array $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }


    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }


    public function asArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current(): mixed
    {
        return current($this->items);
    }

    public function key(): string
    {
        return key($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

  

    public static function makeInstance(array $items): EventValue
    {
        $prepared_items = [];
        foreach ($items as $key => $value) {
            if (is_int($key)) {
                $prepared_items[(string)$key] = $value;
            }
            else {
                $prepared_items[$key] = $value;
            }
        }

        return new EventValue($prepared_items);
    }


}