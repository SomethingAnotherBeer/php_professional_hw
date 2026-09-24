<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class BooleanEventConditionList implements Iterator
{   
    /** @var BooleanEventConditionValue[] $boolean_event_condition_list */
    private array $boolean_event_condition_list;

    private int $position;

    /** @param BooleanEventConditionValue[] $boolean_event_condition_list 
     * @return BooleanEventConditionList
    */
    public static function makeInstance(array $boolean_event_condition_list): BooleanEventConditionList
    {
        return new BooleanEventConditionList($boolean_event_condition_list);
    }

    /** @param BooleanEventConditionValue[] $boolean_event_condition_list */
    public function __construct(array $boolean_event_condition_list)
    {
        $this->boolean_event_condition_list = $boolean_event_condition_list;
        $this->position = 0;
    }

    public function current(): BooleanEventConditionValue
    {
        return $this->boolean_event_condition_list[$this->position];
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
        return isset($this->boolean_event_condition_list[$this->position]);
    }

    /**
     * @return BooleanEventConditionValue[]
     */
    public function all(): array
    {
        return $this->boolean_event_condition_list;
    }

    public function asArray()
    {
        $params = [];
        
        foreach ($this->boolean_event_condition_list as $booleanEventCondition) {
            $params[$booleanEventCondition->getKey()] = $booleanEventCondition->getValue();
        }

        return $params;
    }


}