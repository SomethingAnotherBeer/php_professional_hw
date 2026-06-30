<?php
declare(strict_types=1);
namespace App;

class Three
{
    private Four $four;

    public function __construct(Four $four)
    {
        $this->four = $four;
    }
}