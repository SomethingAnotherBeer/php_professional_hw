<?php
declare(strict_types=1);
namespace App\Model\Event;

use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventValue\EventValueList;

class Event
{
    private ?EventUid $eventUid;

    private ?EventPriority $eventPriority;

    private ?EventConditionList $eventConditionList;

    private ?EventValueList $eventValueList;

    public static function makeInstance
    (
        ?EventUid $eventUid = null,
        ?EventPriority $eventPriority = null,
        ?EventConditionList $eventConditionList = null,
        ?EventValueList $eventValueList = null
    ): Event
    {
        return new Event($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }

    public function __construct
    (
        ?EventUid $eventUid = null,
        ?EventPriority $eventPriority = null,
        ?EventConditionList $eventConditionList = null,
        ?EventValueList $eventValueList = null
    )
    {
        $this->eventUid = $eventUid;
        $this->eventPriority = $eventPriority;
        $this->eventConditionList = $eventConditionList;
        $this->eventValueList = $eventValueList;
    }

    public function getEventUid(): ?string
    {
        return (null !== $this->eventUid) ? $this->eventUid->getEventUid() : null;
    }

    public function getEventPriority(): ?int
    {
        return (null !== $this->eventPriority) ? $this->eventPriority->getEventPriority() : null;
    }

    public function getEventConditionList(): ?EventConditionList
    {
        return $this->eventConditionList;
    }

    public function getEventValueList(): ?EventValueList
    {
        return $this->eventValueList;
    }

}