<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class BooleanEventValueList implements Iterator
{   
    /** @var BooleanEventValue[] $boolean_event_value_list */
    private array $boolean_event_value_list;

    private int $position;

    /** @param BooleanEventValue[] $boolean_event_value_list
     * @return BooleanEventValueList
     */
    public static function makeInstance(array $boolean_event_value_list): BooleanEventValueList
    {   
        return new BooleanEventValueList($boolean_event_value_list);
    }

    /** @param BooleanEventValue[] $boolean_event_value_list */
    public function __construct(array $boolean_event_value_list)
    {
        $this->boolean_event_value_list = $boolean_event_value_list;
        $this->position = 0;
    }

    public function current(): BooleanEventValue
    {
        return $this->boolean_event_value_list[$this->position];
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
        return isset($this->boolean_event_value_list[$this->position]);
    }

    public function asArray(): array
    {
        $params = [];
        foreach ($this->boolean_event_value_list as $booleanEventValue) {
            $params[$booleanEventValue->getKey()] = $booleanEventValue->getValue();
        }

        return $params;
    }


}