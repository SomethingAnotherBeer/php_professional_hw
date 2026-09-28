<?php
declare(strict_types=1);
namespace App\Model\Event\EventValue;

class StringEventValue implements EventValueInterface
{
    private string $key;

    private string $value;

    public static function makeInstance(string $key, string $value): StringEventValue
    {
        return new StringEventValue($key, $value);
    }

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}