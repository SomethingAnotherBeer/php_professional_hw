<?php
declare(strict_types=1);
namespace App\DTO;


class EventConditionListDTO
{   
    private array $condition_list = [];

    public function __construct(array $condition_list = [])
    {
        $this->condition_list = $condition_list;
    }

    public function get(string $key): mixed
    {
        return $this->condition_list[$key] ?? null;
    }

    public function set(string $key, mixed $value)
    {
        $this->condition_list[$key] = $value;
    }

    public function asArray()
    {
        return $this->condition_list;
    }

    public static function makeInstance(array $event_condition_list): EventConditionListDTO
    {
        $eventConditionListDTO = new EventConditionListDTO();
        
        foreach ($event_condition_list as $key => $value) {
            if (is_string($key)) {
                $eventConditionListDTO->set($key, $value);
            }
        }

        return $eventConditionListDTO;
    }




}