<?php
declare(strict_types=1);
namespace App\Model\Event\EventCondition;

class IntegerEventCondition implements EventConditionInterface
{
    private string $key;

    private int $value;

    public static function makeInstance(string $key, int $value): IntegerEventCondition
    {
        return new IntegerEventCondition($key, $value);
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