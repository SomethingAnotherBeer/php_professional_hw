<?php
declare(strict_types=1);
namespace App\Mapper\Event;
use App\Entity\Event;
use App\Factory\ValueObject\EventConditionListFactory;
use App\Factory\ValueObject\EventValueListFactory;
use App\ValueObject\Event\BooleanEventConditionValue;
use App\ValueObject\Event\BooleanEventValue;
use App\ValueObject\Event\EventConditionValueInterface;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;
use App\ValueObject\Event\EventValueInterface;
use App\ValueObject\Event\IntegerEventConditionValue;
use App\ValueObject\Event\IntegerEventValue;
use App\ValueObject\Event\StringEventConditionValue;
use App\ValueObject\Event\StringEventValue;

class ElasticEventMapper
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
        $prepared_event_condition_list = [];
        $prepared_event_value_list = [];

        foreach ($event->getEventConditionList() as $eventCondition) {
            $prepared_event_condition_list[] = $this->prepareEventConditionValueToSource($eventCondition);
        }
        foreach ($event->getEventValueList() as $eventValue) {
            $prepared_event_value_list[] = $this->prepareEventValueToSource($eventValue);
        }

        $data =
        [
            'uid' => $event->getEventUid(),
            'priority' => $event->getEventPriority(),
            'event_condition_list' => $prepared_event_condition_list,
            'event_value_list' => $prepared_event_value_list  
        ];

        return $data;

    }   

    public function makeEntityFromSource(array $params): Event
    {
        $prepared_params = 
        [
            'uid' => $params['uid'],
            'priority' => $params['priority'],
        ];
        
        $prepared_params['event_condition_list'] = [];
        $prepared_params['event_value_list'] = [];

        foreach ($params['event_condition_list'] as $event_condition_params) {
            $prepared_params['event_condition_list'][$event_condition_params['key']] = $this->prepareEventValueFromSource($event_condition_params['value'], $event_condition_params['type']);
        }
        foreach ($params['event_value_list'] as $event_value_params) {
            $prepared_params['event_value_list'][$event_value_params['key']] = $this->prepareEventValueFromSource($event_value_params['value'], $event_value_params['type']);
        }

        $eventUidValue = EventUidValue::makeInstance($prepared_params['uid']);
        $eventPriorityValue = EventPriorityValue::makeInstance($prepared_params['priority']);
        $eventConditionList = $this->eventConditionListFactory->makeEventConditionList($prepared_params['event_condition_list']);
        $eventValueList = $this->eventValueListFactory->makeEventValueList($prepared_params['event_value_list']);

        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValueList);


    }


    public function prepareEventValueFromSource(string $value, string $type): mixed
    {
        $types =
        [
            'integer' => fn(string $value) => (int)$value,
            'boolean' => fn(string $value) => ("true" === $value) ? true : false,
            
        ];
        if (isset($types[$type])) {
            $value = $types[$type]($value);
        }
        return $value;
    }


    public function prepareEventConditionValueToSource(EventConditionValueInterface $eventConditionValue): array
    {
        $transform_mapping_list = 
        [
            IntegerEventConditionValue::class => function(IntegerEventConditionValue $eventConditionValue) {
                return 
                [
                    'key' => $eventConditionValue->getKey(),
                    'value' => (string)$eventConditionValue->getValue(),
                    'type' => 'integer',
                ];
            },
            BooleanEventConditionValue::class => function(BooleanEventConditionValue $eventConditionValue) {
                return 
                [
                    'key' => $eventConditionValue->getKey(),
                    'value' => (true === $eventConditionValue->getValue()) ? "true" : "false",
                    'type' => "boolean",
                ];  
            },
            StringEventConditionValue::class => function(StringEventConditionValue $eventConditionValue) {
                return 
                [
                    'key' => $eventConditionValue->getKey(),
                    'value' => $eventConditionValue->getValue(),
                    'type' => "string",
                ];
            },
        ];

        $eventConditionValueClass = get_class($eventConditionValue);
        return $transform_mapping_list[$eventConditionValueClass]($eventConditionValue);

    }

    public function prepareEventValueToSource(EventValueInterface $eventValue): array
    {
        $transform_mapping_list =
        [
            IntegerEventValue::class => function(IntegerEventValue $eventValue) {
                return
                [
                    'key' => $eventValue->getKey(),
                    'value' => (string)$eventValue->getValue(),
                    'type' => "integer",
                ];
            },
            BooleanEventValue::class => function(BooleanEventValue $eventValue) {
                return
                [
                    'key' => $eventValue->getKey(),
                    'value' => (true === $eventValue->getValue()) ? "true" : "false",
                    'type' => "boolean",
                ];
            },
            StringEventValue::class => function(StringEventValue $eventValue) {
                return 
                [
                    'key' => $eventValue->getKey(),
                    'value' => $eventValue->getValue(),
                    'type' => "string",
                ];
            }
        ];

        $eventValueClass  = get_class($eventValue);
        return $transform_mapping_list[$eventValueClass]($eventValue);
    }


}