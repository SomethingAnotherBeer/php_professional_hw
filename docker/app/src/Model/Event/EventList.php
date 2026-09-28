<?php
declare(strict_types=1);
namespace App\Model\Event;

use Iterator;

class EventList implements Iterator
{   
    /** @var Event[] $event_list */
    private array $event_list;

    private int $position;

    /**
     * @param Event[] $event_list
     * @return EventList
     */
    public static function makeInstance(array $event_list = [])
    {
        return new EventList($event_list);
    }

    /**
     * @param Event[] $event_list
     */
    public function __construct(array $event_list = [])
    {
        $this->event_list = $event_list;
        $this->position = 0;
    }

    public function push(Event $event): static
    {
        $this->event_list[] = $event;
        return $this;
    }

    /**
     * @return Event[]
     */
    public function all(): array
    {
        return $this->event_list;
    }

    public function current(): Event
    {
        return $this->event_list[$this->position];
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
        return isset($this->event_list[$this->position]);
    }


}