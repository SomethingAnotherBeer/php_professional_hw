<?php
declare(strict_types=1);
namespace App\Factory;
use App\DTO\EventDTO;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventUidValue;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventCondition;
use App\ValueObject\Event\EventValueItem;
use App\ValueObject\Event\EventValue;
use App\Entity\Event;
use App\ValueObject\Event\EventContainerList;

class EventContainerFactory
{
    /**
     * @param Event[] $event_list
     */
    public static function makeInstanceListFromEntityList(array $event_list): EventContainerList
    {   
        /** @var EventContainer[] $event_container_list */
        $event_container_list = [];

        foreach ($event_list as $event) {
            $event_container_list[] = static::makeInstanceFromEntity($event);
        }

        return new EventContainerList($event_container_list);
    }

     public static function makeInstanceFromDTO(EventDTO $eventDTO): EventContainer
     {
        $eventUidValue = null;
        $eventPriorityValue = null;
        $eventConditionList = null;
        $eventValue = null;

        if (null !== $eventDTO->getUid()) {
            $eventUidValue = EventUidValue::makeInstance($eventDTO->getUid());
        }

        if (null !== $eventDTO->getPriority()) {
            $eventPriorityValue = EventPriorityValue::makeInstance($eventDTO->getPriority());
        }

        if (null !== $eventDTO->getEventConditionList()) {
            $eventConditionList = EventConditionList::makeInstance($eventDTO->getEventConditionList()->asArray());
        }

        if (null !== $eventDTO->getEventValue()) {
            $eventValue = EventValue::makeInstance($eventDTO->getEventValue()->asArray());
        }

        return EventContainer::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);

     }

    public static function makeInstanceFromEntity(Event $event): EventContainer
    {
        $eventUidValue = EventUidValue::makeInstance($event->getEventUid());
        $eventPriorityValue = EventPriorityValue::makeInstance($event->getEventPriority());
        $eventConditionList = $event->getEventConditionList();
        $eventValue = $event->getEventValue();

        return EventContainer::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);
    }

}