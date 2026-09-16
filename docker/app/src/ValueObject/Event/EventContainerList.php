<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class EventContainerList implements Iterator
{
    /** @var EventContainer[] $event_container_list */
    private array $event_container_list = [];

    private int $position = 0;

    /**
     * @param EventContainer[] $event_container_list
     * @return EventContainerList
     */
    public static function makeInstance(array $event_container_list)
    {
        return new EventContainerList($event_container_list);
    }

    /** 
     * @param EventContainer[] $event_container_list
     */
    public function __construct(array $event_container_list)
    {
        $this->event_container_list = $event_container_list;
        $this->position = 0;
    }

    public function current(): EventContainer
    {
        return $this->event_container_list[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return (isset($this->event_container_list[$this->position]));
    }

    public function asArray(): array
    {
        return array_map(fn(EventContainer $eventContainer) => $eventContainer->asArray(), $this->event_container_list);
    }

}