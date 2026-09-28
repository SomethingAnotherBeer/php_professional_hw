<?php
declare(strict_types=1);
namespace App\Factory\Model\Event;

use App\DTO\Event\EventDTO;
use App\Model\Event\Event;
use App\Model\Event\EventPriority;
use App\Model\Event\EventUid;
use App\Entity\Event\Event as EventEntity;

class EventFactory
{   
    private EventConditionListFactory $eventConditionListFactory;
    
    private EventValueListFactory $eventValueListFactory;

    public function __construct
    (
        EventConditionListFactory $eventConditionListFactory,
        EventValueListFactory $eventValueListFactory
    )
    {
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }


    public function makeEventFromDTO(EventDTO $eventDTO): Event
    {
        $eventUid = null;
        $eventPriority = null;
        $eventConditionList = null;
        $eventValueList = null;

        if (null !== $eventDTO->getEventUid()) {
            $eventUid = EventUid::makeInstance($eventDTO->getEventUid());
        }
        if (null !== $eventDTO->getEventPriority()) {
            $eventPriority = EventPriority::makeInstance($eventDTO->getEventPriority());
        }
        if (null !== $eventDTO->getEventConditionListDTO()) {
            $eventConditionList = $this->eventConditionListFactory->makeFromDTO($eventDTO->getEventConditionListDTO());
        }
        if (null !== $eventDTO->getEventValueListDTO()) {
            $eventValueList = $this->eventValueListFactory->makeFromDTO($eventDTO->getEventValueListDTO());
        }
        return Event::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }

    public function makeEventFromArray(array $params): Event
    {
        $eventUid = null;
        $eventPriority = null;
        $eventConditionList = null;
        $eventValueList = null;

        if (isset($params['event_uid']) && is_string($params['event_uid'])) {
            $eventUid = EventUid::makeInstance($params['event_uid']);
        }
        if (isset($params['event_priority']) && is_int($params['event_priority'])) {
            $eventPriority = EventPriority::makeInstance($params['event_priority']);
        }
        if (isset($params['event_condition_list']) && is_array($params['event_condition_list'])) {
            $eventConditionList = $this->eventConditionListFactory->makeFromArray($params['event_condition_list']);
        }
        if (isset($params['event_value_list']) && is_array($params['event_value_list'])) {
            $eventValueList = $this->eventValueListFactory->makeFromArray($params['event_value_list']);
        }

        return Event::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);

    }

    public function makeEventFromEntity(EventEntity $eventEntity): Event
    {
        $eventUid = EventUid::makeInstance($eventEntity->getEventUid());
        $eventPriority = EventPriority::makeInstance($eventEntity->getEventPriority());
        $eventConditionList = $eventEntity->getEventConditionList();
        $eventValueList = $eventEntity->getEventValueList();

        return Event::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }
    


}