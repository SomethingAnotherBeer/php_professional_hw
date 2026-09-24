<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

interface EventConditionValueInterface
{
    public function getKey(): string;

    public function getValue();
}