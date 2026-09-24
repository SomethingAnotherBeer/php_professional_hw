<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class StringEventConditionList implements Iterator
{
    /** @var StringEventConditionValue[] $string_event_condition_list */
    private array $string_event_condition_list;

    private int $position;

    /** @param StringEventConditionValue[] $string_event_condition_list
     * @return StringEventConditionList
     */
    public static function makeInstance(array $string_event_condition_list): StringEventConditionList
    {
        return new StringEventConditionList($string_event_condition_list);
    }

    /** @param StringEventConditionValue[] $string_event_condition_list */
    public function __construct(array $string_event_condition_list)
    {
        $this->string_event_condition_list = $string_event_condition_list;
        $this->position = 0;
    }

    public function current(): StringEventConditionValue
    {
        return $this->string_event_condition_list[$this->position];
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
        return isset($this->string_event_condition_list[$this->position]);
    }

    /**
     * @return StringEventConditionValue[]
     */
    public function all(): array
    {
        return $this->string_event_condition_list;
    }

    public function asArray(): array
    {
        $params = [];

        foreach ($this->string_event_condition_list as $stringEventCondition) {
            $params[$stringEventCondition->getKey()] = $stringEventCondition->getValue();
        }

        return $params;
    }

}