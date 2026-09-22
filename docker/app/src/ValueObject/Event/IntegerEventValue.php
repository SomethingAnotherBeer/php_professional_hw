<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class IntegerEventValue
{
    private string $key;

    private int $value;

    public static function makeInstance(string $key, int $value): IntegerEventValue
    {
        return new IntegerEventValue($key, $value);
    }

    public function __construct(string $key, int $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}