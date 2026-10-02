<?php
declare(strict_types=1);
namespace App\Model\Event\EventCondition;

use Iterator;

class EventConditionList implements Iterator
{   
    /**
     * @var EventConditionInterface[] $event_condition_list
     */
    private array $event_condition_list;

    private int $position;

    /**
     * @param EventConditionInterface[] $event_condition_list
     * @return EventConditionList
     */
    public static function makeInstance(array $event_condition_list = []): EventConditionList
    {
        return new EventConditionList($event_condition_list);
    }

    /**
     * @param EventConditionInterface[] $event_condition_list
     */
    public function __construct(array $event_condition_list = [])
    {
        $this->event_condition_list = $event_condition_list;
        $this->position = 0;
    }

    public function push(EventConditionInterface $eventConditionInterface): static
    {
        $this->event_condition_list[] = $eventConditionInterface;
        return $this;
    }

    /**
     * @return EventConditionInterface[]
     */
    public function all(): array
    {
        return $this->event_condition_list;   
    }

    public function current(): EventConditionInterface
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

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->event_condition_list[$this->position]);
    }

    public function asArray(): array
    {
        $params = [];
        foreach ($this->event_condition_list as $eventCondition) {
            $params[$eventCondition->getKey()] = $eventCondition->getValue();
        }

        return $params;
    }

}