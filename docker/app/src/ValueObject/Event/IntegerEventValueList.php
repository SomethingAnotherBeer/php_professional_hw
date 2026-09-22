<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class IntegerEventValueList implements Iterator
{   
    /** @var IntegerEventValue[] $integer_event_value_list */
    private array $integer_event_value_list;

    private int $position;

    /** @param IntegerEventValue[] $integer_event_value_list 
     * @return IntegerEventValueList
    */
    public static function makeInstance(array $integer_event_value_list): IntegerEventValueList
    {
        return new IntegerEventValueList($integer_event_value_list);
    }

    /** @param IntegerEventValue[] $integer_event_value_list */
    public function __construct(array $integer_event_value_list)
    {
        $this->integer_event_value_list = $integer_event_value_list;
        $this->position = 0;
    }

    public function current(): IntegerEventValue
    {
        return $this->integer_event_value_list[$this->position];
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
        return isset($this->integer_event_value_list[$this->position]);
    }

    public function asArray(): array
    {
        $params = [];
        foreach ($this->integer_event_value_list as $integerEventValue) {
            $params[$integerEventValue->getKey()] = $integerEventValue->getValue();
        }

        return $params;
    }


}