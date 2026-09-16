<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class EventConditionList implements Iterator
{
    private array $event_condition_list = [];

    public function __construct(array $event_condition_list)
    {
        $this->event_condition_list = $event_condition_list;
    }

    public function get(string $key): mixed
    {
        return $this->event_condition_list[$key] ?? null;
    }

    public function asArray()
    {
        return $this->event_condition_list;
    }
    
    public function count(): int
    {
        return count($this->event_condition_list);
    }

    public function current(): mixed
    {
        return current($this->event_condition_list);
    }

    public function key(): string
    {
        return key($this->event_condition_list);
    }

    public function next(): void
    {
        next($this->event_condition_list);
    }

    public function rewind(): void
    {
        reset($this->event_condition_list);
    }

    public function valid(): bool
    {
        return key($this->event_condition_list) !== null;
    }

    public static function makeInstance(array $event_condition_list): EventConditionList
    {
        $prepared_condition_list = [];

        foreach ($event_condition_list as $key => $value) {
            if (is_string($key)) {
                $prepared_condition_list[$key] = $value;
            }
        }

        return new EventConditionList($prepared_condition_list);
    }


}