<?php
declare(strict_types=1);
namespace App\Interface;

interface ServiceStrategyInterface
{
    public function process(): void;
}