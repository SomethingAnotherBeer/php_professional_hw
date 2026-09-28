<?php
declare(strict_types=1);
namespace App\DTO\Event;

use App\Exception\EventDTO\IntegerEventConditionKeysDiscoveredException;

class EventConditionListDTO
{
    private array $event_condition_list;

    public static function makeInstance(array $event_condition_list)
    {
        return new EventConditionListDTO($event_condition_list);
    }

    public function __construct(array $event_condition_list)
    {   
        $this->check($event_condition_list);
        $this->event_condition_list = $event_condition_list;
    }

    public function getEventConditionList(): array
    {
        return $this->event_condition_list;
    }

    
    private function check(array $event_condition_list): void
    {
        $integer_keys = array_filter($event_condition_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($integer_keys) > 0) {
            $integer_keys_msg = implode(", ", $integer_keys);
            throw new IntegerEventConditionKeysDiscoveredException("Список условий события должен иметь только строковые ключи. Обнаружены следуюие целочисленные ключи: $integer_keys_msg");
        }
    }

}