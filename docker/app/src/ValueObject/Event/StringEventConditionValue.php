<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class StringEventConditionValue
{
    private string $key;

    private string $value;

    public static function makeInstance(string $key, bool $value): BooleanEventConditionValue
    {
        return new BooleanEventConditionValue($key, $value);
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