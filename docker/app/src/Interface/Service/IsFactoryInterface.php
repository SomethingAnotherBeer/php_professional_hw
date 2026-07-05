<?php
declare(strict_types=1);
namespace App\Interface\Service;

interface IsFactoryInterface
{
    public static function makeInstance(): static;
    
}