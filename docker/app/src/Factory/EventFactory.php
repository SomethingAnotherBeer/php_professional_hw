<?php
declare(strict_types=1);
namespace App\Factory;

use App\ValueObject\Event\Event;
use App\ValueObject\Event\EventCondition;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventContainerList;

class EventFactory
{

    public function makeEventContainerList(array $params) 
    {   
        /** @var EventContainer[] $event_container_list */
        $event_container_list = [];

        foreach ($params as $event_container_params) {
            $event_container_list[] = $this->makeEventContainer($event_container_params);
        }

        return new EventContainerList($event_container_list);
    }


    public function makeEventContainer(array $params): EventContainer
    {
        $eventContainer = new EventContainer();

        if (isset('uid') && is_string($params['uid'])) {
            $eventContainer->setUid($params['uid']);
        }

        if (isset($params['priority']) && is_int($params['priority'])) {
            $eventContainer->setPriority($params['priority']);
        }

        if (isset($params['event_condition_list']) && is_array($params['event_condition_list'])) {
            $eventContainer->setEventConditionList($this->makeEventConditionList($params['event_condition_list']));
        }

        if (isset($params['event']) && is_array($params['event'])) {
            $eventContainer->setEvent($this->makeEvent($params['event']));
        }

        return $eventContainer;
    }

    public function makeEventConditionList(array $params): EventConditionList
    {
        /** @var EventCondition[] $event_condition_list */
        $event_condition_list = [];
        
        foreach ($params as $event_condition_params) {
            $currentEventCondition = $this->makeEventCondition($event_condition_params);
            if ($currentEventCondition) {
                $event_condition_list[] = $currentEventCondition;
            }
        }

        return new EventConditionList($event_condition_list);


    }

    public function makeEventCondition(array $params): ?EventCondition
    {
        $eventCondition = null;

        if (isset($params['key']) && is_string($params['key']) 
            && isset($params['value'])) {

            $eventCondition = new EventCondition();
            $eventCondition->setKey($params['key'])->setValue($params['value']);

        }

        return $eventCondition;
    }

    public function makeEvent(array $params): Event
    {
        $params = array_filter($params, fn(int|string $key) => is_string($key), ARRAY_FILTER_USE_KEY);
        return new Event($params);
    }


}