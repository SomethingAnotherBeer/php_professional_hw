<?php
declare(strict_types=1);
namespace App\Model\Event;

use App\Exception\Event\EventPriorityCannotBeLessOrEqualZeroException;

class EventPriority
{
    private int $event_priority;

    public static function makeInstance(int $event_priority): EventPriority
    {
        return new EventPriority($event_priority);
    }

    public function __construct(int $event_priority)
    {
        $this->checkEventPriority($event_priority);
        $this->event_priority = $event_priority;
    }

    public function getEventPriority(): int
    {
        return $this->event_priority;
    }

    private function checkEventPriority(int $event_priority): void
    {
        if ($event_priority <= 0) {
            throw new EventPriorityCannotBeLessOrEqualZeroException("Приоритет события не может быть меньше или равен нулю");
        }
    }
}