<?php
declare(strict_types=1);
namespace App\ValueObject\Condition;

class BooleanValue
{
    public readonly bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }
}