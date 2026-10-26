<?php
declare(strict_types=1);
namespace App\Factory\Model\Event;

use App\DTO\Event\EventConditionListDTO;
use App\Exception\Event\UndefinedEventConditionTypeException;
use App\Exception\EventDTO\IntegerEventConditionKeysDiscoveredException;
use App\Model\Event\EventCondition\BooleanEventCondition;
use App\Model\Event\EventCondition\EventConditionInterface;
use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventCondition\IntegerEventCondition;
use App\Model\Event\EventCondition\StringEventCondition;

class EventConditionListFactory
{
    public function makeFromDTO(EventConditionListDTO $eventConditionListDTO): EventConditionList
    {
        $eventConditionList = EventConditionList::makeInstance();
        foreach ($eventConditionListDTO->getEventConditionList() as $key => $value) {
            $eventConditionList->push($this->makeItem($key, $value));
        }

        return $eventConditionList;
    }

    public function makeFromArray(array $event_condition_list): EventConditionList
    {
        $integer_keys_params = array_filter($event_condition_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($integer_keys_params) > 0) {
            $integer_keys = array_keys($integer_keys_params);
            $integer_keys_msg = implode(", ", $integer_keys);
            throw new IntegerEventConditionKeysDiscoveredException("Список условий события должен иметь только строковые ключи. Обнаружены следующие целочисленные ключи: $integer_keys_msg");
        }
        $eventConditionList = EventConditionList::makeInstance();
        foreach ($event_condition_list as $key => $value) {
            $eventConditionList->push($this->makeItem($key, $value));
        }

        return $eventConditionList;

    }

    protected function makeItem(string $key, mixed $value): EventConditionInterface
    {
        $match = 
        [
            'integer' => fn(string $key, int $value): IntegerEventCondition => IntegerEventCondition::makeInstance($key, $value),
            'boolean' => fn(string $key, bool $value): BooleanEventCondition => BooleanEventCondition::makeInstance($key, $value),
            'string' => fn(string $key, string $value): StringEventCondition => StringEventCondition::makeInstance($key, $value),
        ];

        $type = gettype($value);
        if (!isset($match[$type])) {
            throw new UndefinedEventConditionTypeException("Тип $type не предусмотрен в качестве значения условия события для ключа $key");
        }

        return $match[$type]($key, $value);


    }


}