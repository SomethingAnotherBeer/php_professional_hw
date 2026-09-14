<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class Event
{
    private array $event_items = [];

    public function __construct(array $params = [])
    {
        $this->event_items = $params;
    }


    public function __get(string $name): mixed
    {
        return $this->event_items[$name] ?? null;
    }

    public function __set(string $name, mixed $value)
    {
        $this->event_items[$name] = $value;
    }
    
    public function asArray(): array
    {
        return $this->event_items;
    }

    public function count(): int
    {
        return count($this->event_items);
    }


}