<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class EventCondition
{
    private string $key;

    private mixed $value;

    public function __construct(array $params = [])
    {
        $this->key = $params['key'];
        $this->value = $params['value'];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function asArray(): array
    {
        return 
        [
            'key' => $this->key,
            'value' => $this->value,
        ];

    }


}