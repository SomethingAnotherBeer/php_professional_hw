<?php
declare(strict_types=1);
namespace App\DTO;


class EventValueDTO
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

    public static function makeInstance(array $event_value_params): EventValueDTO
    {
        $eventValueDTO = new EventValueDTO();

        foreach ($event_value_params as $key => $value) {
            if (is_int($key)) {
                $eventValueDTO->set((string)$key, $value);
            }
            else {
                $eventValueDTO->set($key, $value);
            }
        }

        return $eventValueDTO;
    }

}