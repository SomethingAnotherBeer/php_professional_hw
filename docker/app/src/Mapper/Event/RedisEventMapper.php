<?php
declare(strict_types=1);
namespace App\Mapper\Event;

use App\Entity\Event;
use App\Factory\ValueObject\EventConditionListFactory;
use App\Factory\ValueObject\EventValueListFactory;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;

class RedisEventMapper
{
    private EventConditionListFactory $eventConditionListFactory;
    private EventValueListFactory $eventValueListFactory;

    public function __construct(EventConditionListFactory $eventConditionListFactory, EventValueListFactory $eventValueListFactory)
    {
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }


    public function makeSourceFromEntity(Event $event): array
    {
        return
        [
            'uid' => $event->getEventUid(),
            'priority' => $event->getEventPriority(),
            'event_condition_list' => $event->getEventConditionList()->asArray(),
            'event_value_list' => $event->getEventValueList()->asArray(),
        ];
    }
    
    public function makeEntityFromSource(array $params): Event
    {

        $eventUidValue = EventUidValue::makeInstance($params['uid']);
        $eventPriorityValue = EventPriorityValue::makeInstance($params['priority']);
        $eventConditionList = $this->eventConditionListFactory->makeEventConditionList($params['event_condition_list']);
        $eventValueList = $this->eventValueListFactory->makeEventValueList($params['event_value_list']);

        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);


    }
}