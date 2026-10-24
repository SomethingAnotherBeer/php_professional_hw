<?php
declare(strict_types=1);
namespace App\Mapper\Event;

use App\Entity\Event\Event;
use App\Exception\Event\EventSchemaInvalidException;
use App\Factory\Model\Event\EventConditionListFactory;
use App\Factory\Model\Event\EventValueListFactory;
use App\Model\Event\EventPriority;
use App\Model\Event\EventUid;

class MongoEventMapper
{
    private EventConditionListFactory $eventConditionListFactory;
    private EventValueListFactory $eventValueListFactory;

    public function __construct(EventConditionListFactory $eventConditionListFactory, EventValueListFactory $eventValueListFactory)
    {
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }


    public function fromEntityToSource(Event $event): array
    {
        return 
        [
            '_id' => $event->getEventUid(),
            'event_uid' => $event->getEventUid(),
            'event_priority' => $event->getEventPriority(),
            'event_condition_list' => $event->getEventConditionList()->asArray(),
            'event_value_list' => $event->getEventValueList()->asArray(),
        ];
    }

    public function fromSourceToEntity(array $event_params): Event
    {
        $schema_errors = $this->getSchemaErrors($event_params);
        if (count($schema_errors) > 0) {
            $schema_errors_msg = implode("\n", $schema_errors);
            throw new EventSchemaInvalidException($schema_errors_msg);
        }

        $eventUid = EventUid::makeInstance($event_params['event_uid']);
        $eventPriority = EventPriority::makeInstance($event_params['event_priority']);
        $eventConditionList = $this->eventConditionListFactory->makeFromArray($event_params['event_condition_list']);
        $eventValueList = $this->eventValueListFactory->makeFromArray($event_params['event_value_list']);

        return Event::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }


    protected function getSchemaErrors(array $event_params): array
    {
        $errors = [];

        if (!isset($event_params['event_uid']) || !is_string($event_params['event_uid'])) {
            $errors[] = "Идентификатор события не найден"; 
        }
        if (!isset($event_params['event_priority']) || !is_int($event_params['event_priority'])) {
            $errors[] = "Приоритет события не найден";
        }   
        if (!isset($event_params['event_condition_list']) || !is_array($event_params['event_condition_list'])) {
            $errors[] = "Список условий события не найден";
        }
        if (!isset($event_params['event_value_list']) || !is_array($event_params['event_value_list'])) {
            $errors[] = "Список значений события не найден";
        }
        
        $event_condition_type_list =
        [       
            'integer' => true,
            'boolean' => true,
            'string' => true,
        ];  
        $event_value_type_list =
        [
            'integer' => true,
            'boolean' => true,
            'string' => true,
        ];

        foreach ($event_params['event_condition_list'] as $event_condition_param_index => $event_condition_param) {
            $type = gettype($event_condition_param);
            if (!isset($event_condition_type_list[$type]) || true !== $event_condition_type_list[$type]) {
                $errors[] = "Условие возникновения события с индексом $event_condition_param_index имеет недопустимый тип $type";
            }
        }
        foreach ($event_params['event_value_list'] as $event_value_param_index => $event_value_param) {
            $type = gettype($event_value_param);
            if (!isset($event_value_type_list[$type]) || true !== $event_value_type_list[$type]) {
                $errors[] = "Значение события с индексом $event_value_param_index имеет недопустимый тип $type";
            }
        }

        return $errors;
    }



}