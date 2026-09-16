<?php
declare(strict_types=1);
namespace App\ValueObject\Event;


class EventContainer
{
    private ?EventUidValue $eventUidValue;
    private ?EventPriorityValue $eventPriorityValue;
    private ?EventConditionList $eventConditionList;
    private ?EventValue $eventValue;


    public function __construct(
        ?EventUidValue $eventUidValue = null,
        ?EventPriorityValue $eventPriorityValue = null,
        ?EventConditionList $eventConditionList = null,
        ?EventValue $eventValue = null
    )
    {
        $this->eventUidValue = $eventUidValue ?? null;
        $this->eventPriorityValue = $eventPriorityValue ?? null;
        $this->eventConditionList = $eventConditionList ?? null;
        $this->eventValue = $eventValue ?? null;
    }

    public function getEventUid(): ?string
    {
        return (null !== $this->eventUidValue) ? $this->eventUidValue->getUid() : null;
    }

    public function getEventPriority(): ?int
    {
        return (null !== $this->eventPriorityValue) ? $this->eventPriorityValue->getEventPriority() : null;
    }

    public function getEventConditionList(): ?EventConditionList
    {
        return $this->eventConditionList;
    }

    public function getEventValue(): ?EventValue
    {
        return $this->eventValue;
    }

    public function asArray(): array
    {
        $params =
        [
            'uid' => (null !== $this->eventUidValue) ? $this->eventUidValue->getUid() : null,
            'priority' => (null !== $this->eventPriorityValue) ? $this->eventPriorityValue->getEventPriority() : null,
            'event_condition_list' => (null !== $this->eventConditionList) ? $this->eventConditionList->asArray() : null,
            'event_value' => (null !== $this->eventValue) ? $this->eventValue->asArray() : null,
        ];

        return array_filter($params, fn(mixed $param) => null !== $param);
    }


    public static function makeInstance(
        ?EventUidValue $eventUidValue = null,
        ?EventPriorityValue $eventPriorityValue = null,
        ?EventConditionList $eventConditionList = null,
        ?EventValue $eventValue = null
    )
    {   
        return new EventContainer($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);
    }




    
}