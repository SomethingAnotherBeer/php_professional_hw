<?php
declare(strict_types=1);
namespace App\Entity\Event;

use App\Model\Event\Event as EventModel;
use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventPriority;
use App\Model\Event\EventUid;
use App\Model\Event\EventValue\EventValueList;

class Event
{
    private EventUid $eventUid;
    
    private EventPriority $eventPriority;

    private EventConditionList $eventConditionList;

    private EventValueList $eventValueList;

    public function __construct
    (
        EventUid $eventUid,
        EventPriority $eventPriority,
        EventConditionList $eventConditionList,
        EventValueList $eventValueList
    )
    {
        $this->eventUid = $eventUid;
        $this->eventPriority = $eventPriority;
        $this->eventConditionList = $eventConditionList;
        $this->eventValueList = $eventValueList;
    }

    public function getEventUid(): string
    {
        return $this->eventUid->getEventUid();
    }

    public function setEventUid(EventUid $eventUid): static
    {
        $this->eventUid = $eventUid;
        return $this;
    }
    
    public function getEventPriority(): int
    {
        return $this->eventPriority->getEventPriority();
    }

    public function setEventPriority(EventPriority $eventPriority): static
    {
        $this->eventPriority = $eventPriority;
        return $this;
    }

    public function getEventConditionList(): EventConditionList
    {
        return $this->eventConditionList;
    }

    public function setEventConditionList(EventConditionList $eventConditionList): static
    {
        $this->eventConditionList = $eventConditionList;
        return $this;
    }

    public function getEventValueList(): EventValueList
    {
        return $this->eventValueList;
    }

    public function setEventValueList(EventValueList $eventValueList): static
    {
        $this->eventValueList = $eventValueList;
        return $this;
    }

    public static function makeInstance
    (
        EventUid $eventUid,
        EventPriority $eventPriority,
        EventConditionList $eventConditionList,
        EventValueList $eventValueList
    ): Event
    {
        return new Event($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }

}