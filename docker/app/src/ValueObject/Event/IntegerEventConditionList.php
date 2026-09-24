<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class IntegerEventConditionList implements Iterator
{   
    /** @var IntegerEventConditionValue[] $integer_event_condition_list */
    private array $integer_event_condition_list;

    private int $position;

    /** @param IntegerEventConditionValue[] $integer_event_condition_list 
     * @return IntegerEventConditionList
    */
    public static function makeInstance(array $integer_event_condition_list): IntegerEventConditionList
    {
        return new IntegerEventConditionList($integer_event_condition_list);
    }

    /** @param IntegerEventConditionValue[] $integer_event_condition_list */
    public function __construct(array $integer_event_condition_list)
    {
        $this->integer_event_condition_list = $integer_event_condition_list;
        $this->position = 0;
    }

    public function current(): IntegerEventConditionValue
    {
        return $this->integer_event_condition_list[$this->position];
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
        return isset($this->integer_event_condition_list[$this->position]);
    }

    /**
     * @return IntegerEventConditionValue[]
     */
    public function all(): array
    {
        return $this->integer_event_condition_list;
    }

    public function asArray(): array
    {
        $params = [];

        foreach ($this->integer_event_condition_list as $integerEventCondition) {
            $param[$integerEventCondition->getKey()] = $integerEventCondition->getValue();
        }

        return $params;
    }


}