<?php
declare(strict_types=1);
namespace App\Model\Event\EventCondition;

interface EventConditionInterface
{
    public function getKey(): string;

    public function getValue();
}