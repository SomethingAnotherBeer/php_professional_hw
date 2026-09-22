<?php
declare(strict_types=1);
namespace App\DTO;


class EventValueListDTO
{
    private array $event_value_params = [];

    public function __construct(array $event_value_params = [])
    {
        $this->event_value_params = $event_value_params;
    }

    public function get(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    public function set(string $key, mixed $value): static
    {
        $this->event_value_params[$key] = $value;

        return $this;
    }

    public function asArray(): array
    {
        return $this->event_value_params;
    }

    public function all(): array
    {
        return $this->event_value_params;
    }

    public static function makeInstance(array $event_value_params): EventValueListDTO
    {
        return new EventValueListDTO($event_value_params);
    }

}