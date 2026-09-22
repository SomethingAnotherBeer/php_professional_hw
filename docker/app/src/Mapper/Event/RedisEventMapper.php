<?php
declare(strict_types=1);
namespace App\Mapper\Event;

use App\Entity\Event;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventValueListNotSpecifiedException;
use App\Exception\Value\ValueMustBeArrayException;
use App\Exception\Value\ValueMustBeIntegerException;
use App\Exception\Value\ValueMustBeStringException;
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
        if (!isset($params['uid'])) {
            throw new EventUidNotSpecifiedException("Отсутствует идентификатор события");
        }

        if (!isset($params['priority'])) {
            throw new EventPriorityNotSpecifiedException("Отсутствует приоритет события");
        }

        if (!isset($params['event_condition_list'])) {
            throw new EventConditionListNotSpecifiedException("Отсутствует списко условий события");
        }

        if (!isset($params['event_value_list'])) {
            throw new EventValueListNotSpecifiedException("Отсутствует список параметров события");
        }

        if (!is_string($params['uid'])) {
            throw new ValueMustBeStringException("Идентификатор события не является строковым значением");
        }

        if (!is_int($params['priority'])) {
            throw new ValueMustBeIntegerException("Приоритет события не является целым числом");
        }

        if (!is_array($params['event_condition_list'])) {
            throw new ValueMustBeArrayException("Список условий события не является массивом");
        }

        if (!is_array($params['event_value_list'])) {
            throw new ValueMustBeArrayException("Список параметров события не является массивом");
        }

        $eventUidValue = EventUidValue::makeInstance($params['uid']);
        $eventPriorityValue = EventPriorityValue::makeInstance($params['priority']);
        $eventConditionList = $this->eventConditionListFactory->makeEventConditionList($params['event_condition_list']);
        $eventValueList = $this->eventValueListFactory->makeEventValueList($params['event_value_list']);

        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);


    }
}