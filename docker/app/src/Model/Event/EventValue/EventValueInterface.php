<?php
declare(strict_types=1);
namespace App\Model\Event\EventValue;

interface EventValueInterface
{
    public function getKey(): string;

    public function getValue();
}