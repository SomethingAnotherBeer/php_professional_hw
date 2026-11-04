<?php
declare(strict_types=1);
namespace App\Model\Event\EventCondition;

class StringEventCondition implements EventConditionInterface
{
    private string $key;
    
    private string $value;

    public static function makeInstance(string $key, string $value): StringEventCondition
    {
        return new StringEventCondition($key, $value);
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