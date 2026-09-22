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
        return new EventConditionListDTO($event_condition_list);
    }




}