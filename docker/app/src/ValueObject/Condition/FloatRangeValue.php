<?php
declare(strict_types=1);
namespace App\ValueObject\Condition;

class FloatRangeValue
{
    public readonly float $from;

    public readonly float $to;

    public function __construct(float $from, float $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}