<?php
declare(strict_types=1);
namespace App;

class One
{
    private SomeInterface $innerInstance;
    private Three $three;
    private int $someValue;

    public function __construct(SomeInterface $innerInstance, Three $three, int $someValue)
    {
        $this->innerInstance = $innerInstance;
        $this->three = $three;
        $this->someValue = $someValue;

    }

    public function doSomething(int $arg): int
    {
        return $arg * $arg;
    }
}