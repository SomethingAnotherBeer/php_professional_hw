<?php
declare(strict_types=1);
namespace App\ValueObject\Condition;

class StringValue
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}