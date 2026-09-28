<?php
declare(strict_types=1);
namespace App\Model\Event\EventValue;

class BooleanEventValue implements EventValueInterface
{
    private string $key;

    private bool $value;

    public static function makeInstance(string $key, bool $value): BooleanEventValue
    {
        return new BooleanEventValue($key, $value);
    }

    public function __construct(string $key, bool $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}