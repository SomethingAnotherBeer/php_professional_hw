<?php
declare(strict_types=1);
namespace App\Factory\Model\Event;

use App\DTO\Event\EventValueListDTO;
use App\Exception\Event\UndefinedEventValueTypeException;
use App\Exception\EventDTO\IntegerEventValueKeysDiscoveredException;
use App\Model\Event\EventValue\BooleanEventValue;
use App\Model\Event\EventValue\EventValueInterface;
use App\Model\Event\EventValue\EventValueList;
use App\Model\Event\EventValue\IntegerEventValue;
use App\Model\Event\EventValue\StringEventValue;

class EventValueListFactory
{
    public function makeFromDTO(EventValueListDTO $eventValueListDTO)
    {   
        $eventValueList = EventValueList::makeInstance();
        foreach ($eventValueListDTO->getEventValueList() as $key => $value) {
            $eventValueList->push($this->makeItem($key, $value));
        }

        return $eventValueList;
    }

    public function makeFromArray(array $event_value_list): EventValueList
    {
        $integer_keys = array_filter($event_value_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($integer_keys) > 0) {
            $integer_keys_msg = implode(", ", $integer_keys);
            throw new IntegerEventValueKeysDiscoveredException("Список значений события должен иметь только строковые ключи. Обнаружены следуюие целочисленные ключи: $integer_keys_msg");
        }
        $eventValueList = EventValueList::makeInstance();
        
        foreach ($event_value_list as $key => $value) {
            $eventValueList->push($this->makeItem($key, $value));
        }

        return $eventValueList;
    }

    protected function makeItem(string $key, mixed $value): EventValueInterface
    {
        $match = 
        [
            'integer' => fn(string $key, int $value): IntegerEventValue => IntegerEventValue::makeInstance($key, $value),
            'boolean' => fn(string $key, bool $value): BooleanEventValue => BooleanEventValue::makeInstance($key, $value),
            'string' => fn(string $key, string $value): StringEventValue => StringEventValue::makeInstance($key, $value),
        ];

        $type = gettype($value);
        if (!isset($match[$type])) {
            throw new UndefinedEventValueTypeException("Тип $type не предусмотрен в качестве значения события для ключа $key");
        }

        return $match[$type]($key, $value);
    }

}