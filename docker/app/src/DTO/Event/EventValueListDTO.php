<?php
declare(strict_types=1);
namespace App\DTO\Event;

use App\Exception\EventDTO\IntegerEventValueKeysDiscoveredException;

class EventValueListDTO
{
    private array $event_value_list;

    public static function makeInstance(array $event_value_list)
    {
        return new EventValueListDTO($event_value_list);
    }

    public function __construct(array $event_value_list)
    {
        $this->check($event_value_list);
        $this->event_value_list = $event_value_list;
    }

    public function getEventValueList(): array
    {
        return $this->event_value_list;
    }

    private function check(array $event_value_list): void
    {
        $integer_keys = array_filter($event_value_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($integer_keys) > 0) {
            $integer_keys = array_keys($integer_keys);
            $integer_keys_msg = implode(', ', $integer_keys);
            throw new IntegerEventValueKeysDiscoveredException("Список значений события должен иметь только строковые ключи. Обнаружены следующие целочисленные ключи: $integer_keys_msg");
        }
    }
}