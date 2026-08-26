<?php
declare(strict_types=1);
namespace App\ValueObject\Condition;

class IntegerRangeValue
{
    public readonly int $from;

    public readonly int $to;

    public function __construct(int $from, int $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}