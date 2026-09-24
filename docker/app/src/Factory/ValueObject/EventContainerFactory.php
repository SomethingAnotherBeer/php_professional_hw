<?php
declare(strict_types=1);
namespace App\Factory\ValueObject;

use App\DTO\EventDTO;
use App\Entity\Event;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventContainerList;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;

class EventContainerFactory
{
    private EventConditionListFactory $eventConditionListFactory;
    private EventValueListFactory $eventValueListFactory;

    public function __construct(EventConditionListFactory $eventConditionListFactory, EventValueListFactory $eventValueListFactory)
    {
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }


    public function makeEventContainerFromDTO(EventDTO $eventDTO): EventContainer
    {
        $eventUidValue = null;
        $eventPriorityValue = null;
        $eventConditionList = null;
        $eventValueList = null;

        if (null !== $eventDTO->getUid()) {
            $eventUidValue = EventUidValue::makeInstance($eventDTO->getUid());
        }

        if (null !== $eventDTO->getPriority()) {
            $eventPriorityValue = EventPriorityValue::makeInstance($eventDTO->getPriority());
        }

        if (null !== $eventDTO->getEventConditionList()) {
            $eventConditionList = $this->eventConditionListFactory->makeEventConditionList($eventDTO->getEventConditionList()->asArray());
        }

        if (null !== $eventDTO->getEventValueList()) {
            $eventValueList = $this->eventValueListFactory->makeEventValueList($eventDTO->getEventValueList()->asArray());
        }

        return EventContainer::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);
    }

    public function makeEventContainerFromEntity(Event $event): EventContainer
    {
        $eventUidValue = EventUidValue::makeInstance($event->getEventUid());
        $eventPriorityValue = EventPriorityValue::makeInstance($event->getEventPriority());
        $eventConditionList = $event->getEventConditionList();
        $eventValueList = $event->getEventValueList();

        return EventContainer::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);
    }
    
    /** @param Event[] $event_list */
    public function makeEventContainerListFromEntityList(array $event_list): EventContainerList
    {   
        /** @var EventContainer[] $event_container_list */
        $event_container_list = array_map(fn(Event $event) => $this->makeEventContainerFromEntity($event), $event_list);
        return EventContainerList::makeInstance($event_container_list);
    }


}