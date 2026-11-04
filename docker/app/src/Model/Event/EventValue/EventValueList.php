<?php
declare(strict_types=1);
namespace App\Model\Event\EventValue;

use Iterator;

class EventValueList implements Iterator
{
    /**
     * @var EventValueInterface[] $event_value_list
     */
    private array $event_value_list;

    private int $position;

    /**
     * @param EventValueInterface[] $event_value_list
     * @return EventValueList
     */
    public static function makeInstance(array $event_value_list = []): EventValueList
    {
        return new EventValueList($event_value_list);
    }

    /**
     * @param EventValueInterface[] $event_value_list
     */
    public function __construct(array $event_value_list = [])
    {
        $this->event_value_list = $event_value_list;
        $this->position = 0;
    }

    public function push(EventValueInterface $eventValueInterface): static
    {
        $this->event_value_list[] = $eventValueInterface;
        return $this;
    }

    /**
     * @return EventValueInterface[]
     */
    public function all(): array
    {
        return $this->event_value_list;
    }

    public function current(): EventValueInterface
    {
        return $this->event_value_list[$this->position];
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
        return isset($this->event_value_list[$this->position]);
    }

    public function asArray(): array
    {
        $params = [];
        foreach ($this->event_value_list as $eventValue) {
            $params[$eventValue->getKey()] = $eventValue->getValue();
        }

        return $params;
    }
}