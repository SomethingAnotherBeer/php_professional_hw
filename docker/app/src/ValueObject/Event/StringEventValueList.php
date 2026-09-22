<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

use Iterator;

class StringEventValueList implements Iterator
{   
    /** @var StringEventValue[] $string_event_value_list */
    private array $string_event_value_list;

    private int $position;

    /** @param StringEventValue[] $string_event_value_list 
     * @return StringEventValueList
    */
    public static function makeInstance(array $string_event_value_list): StringEventValueList
    {
        return new StringEventValueList($string_event_value_list);
    }

    /** @param StringEventValue[] $string_event_value_list */
    public function __construct(array $string_event_value_list)
    {
        $this->string_event_value_list = $string_event_value_list;
        $this->position = 0;
    }

    public function current(): StringEventValue
    {
        return $this->string_event_value_list[$this->position];
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
        return isset($this->string_event_value_list[$this->position]);
    }

    public function asArray(): array
    {
        $params = [];
        foreach ($this->string_event_value_list as $stringEventValue) {
            $params[$stringEventValue->getKey()] = $stringEventValue->getValue();
        }

        return $params;
    }
}