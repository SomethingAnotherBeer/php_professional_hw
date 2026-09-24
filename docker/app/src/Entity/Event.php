<?php
declare(strict_types=1);
namespace App\Entity;


use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;
use App\ValueObject\Event\EventValueList;

class Event
{
    private EventUidValue $eventUidValue;

    private EventPriorityValue $eventPriorityValue;

    private EventConditionList $eventConditionList;

    private EventValueList $eventValueList;

    public function __construct
    (
        EventUidValue $eventUidValue,
        EventPriorityValue $eventPriorityValue,
        EventConditionList $eventConditionList,
        EventValueList $eventValueList
    )
    {
        $this->eventUidValue = $eventUidValue;
        $this->eventPriorityValue = $eventPriorityValue;
        $this->eventConditionList = $eventConditionList;
        $this->eventValueList = $eventValueList;
    }

    public function asArray(): array
    {
        return 
        [
            'uid' => $this->eventUidValue->getUid(),
            'priority' => $this->eventPriorityValue->getEventPriority(),
            'event_condition_list' => $this->eventConditionList->asArray(),
            'event_value_list' => $this->eventValueList->asArray(),
        ];   
    }


    public function getEventUid(): string
    {
        return $this->eventUidValue->getUid();
    }

    public function setEventUid(EventUidValue $eventUidValue): static
    {
        $this->eventUidValue = $eventUidValue;

        return $this;
    }

    public function getEventPriority(): int
    {
        return $this->eventPriorityValue->getEventPriority();
    }
    
    public function setEventPriority(EventPriorityValue $eventPriorityValue): static
    {
        $this->eventPriorityValue = $eventPriorityValue;

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

    public static function makeInstance(
        EventUidValue $eventUidValue,
        EventPriorityValue $eventPriorityValue,
        EventConditionList $eventConditionList,
        EventValueList $eventValueList
    ): Event
    {
        return new Event($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);
    }


}