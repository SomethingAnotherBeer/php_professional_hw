<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class EventConditionList implements Iterator
{
    /** @var EventCondition[] $event_condition_list */
    private array $event_condition_list;

    private int $position = 0;


    /** 
     * @param EventCondition[] $event_condition_list
     */
    public function __construct(array $event_condition_list)
    {
        $this->event_condition_list = $event_condition_list;
        $this->position = 0;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): EventCondition
    {
        return $this->event_condition_list[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->event_condition_list[$this->position]);
    }

    public function asArray(): array
    {
        $condition_list_args = array_map(fn(EventCondition $eventCondition) => $eventCondition->asArray(), $this->event_condition_list);
        $key_value_list_args = [];

        foreach ($condition_list_args as $condition_list_arg_key => $condition_list_arg_value) {
            $key_value_list_args[$condition_list_arg_key] = $condition_list_arg_value;
        }

        return $key_value_list_args;


    }

    public function count(): int
    {
        return count($this->event_condition_list);
    }


}