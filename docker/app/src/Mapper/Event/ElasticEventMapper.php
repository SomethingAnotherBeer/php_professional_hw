<?php
declare(strict_types=1);
namespace App\Mapper\Event;

use App\Entity\Event\Event;
use App\Exception\Event\EventSchemaInvalidException;
use App\Factory\Model\Event\EventConditionListFactory;
use App\Factory\Model\Event\EventValueListFactory;
use App\Model\Event\EventCondition\BooleanEventCondition;
use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventCondition\IntegerEventCondition;
use App\Model\Event\EventCondition\StringEventCondition;
use App\Model\Event\EventPriority;
use App\Model\Event\EventUid;
use App\Model\Event\EventValue\BooleanEventValue;
use App\Model\Event\EventValue\EventValueList;
use App\Model\Event\EventValue\IntegerEventValue;
use App\Model\Event\EventValue\StringEventValue;

class ElasticEventMapper
{
    public const INTEGER_EVENT_CONDITION_TYPE = 'integer';
    public const BOOLEAN_EVENT_CONDITION_TYPE = 'boolean';
    public const STRING_EVENT_CONDITION_TYPE = 'string';

    public const INTEGER_EVENT_VALUE_TYPE = 'integer';
    public const BOOLEAN_EVENT_VALUE_TYPE = 'boolean';
    public const STRING_EVENT_VALUE_TYPE = 'string';


    public function fromEntityToSource(Event $event): array
    {   
        $event_condition_list = $this->mapEventConditionListToSource($event->getEventConditionList());
        $event_value_list = $this->mapEventValueListToSource($event->getEventValueList());

        return 
        [
            'event_uid' => $event->getEventUid(),
            'event_priority' => $event->getEventPriority(),
            'event_condition_list' => $event_condition_list,
            'event_value_list' => $event_value_list,
            'event_condition_list_count' => count($event_condition_list),
            'event_value_list_count' => count($event_value_list),
            
        ];
    }

    public function fromSourceToEntity(array $event_params): Event
    {
        $errors = $this->getSchemaErrors($event_params);
        if (count($errors) > 0) {
            $errors_msg = implode("\n", $errors);
            throw new EventSchemaInvalidException($errors_msg);
        }

        $eventUid = EventUid::makeInstance($event_params['event_uid']);
        $eventPriority = EventPriority::makeInstance($event_params['event_priority']);
        $eventConditionList = $this->mapEventConditionListFromSource($event_params['event_condition_list']);
        $eventValueList = $this->mapEventValueListFromSource($event_params['event_value_list']);

        return Event::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);
    }


    public function mapEventConditionListToSource(EventConditionList $eventConditionList): array
    {
        $source = [];

        $types =
        [
            IntegerEventCondition::class => fn(IntegerEventCondition $eventCondition) => 
            [
                'key' => $eventCondition->getKey(),
                'value' => (string)$eventCondition->getValue(),
                'type' => static::INTEGER_EVENT_CONDITION_TYPE,
            ],
            BooleanEventCondition::class => fn(BooleanEventCondition $eventCondition) =>
            [
                'key' => $eventCondition->getKey(),
                'value' => true === $eventCondition->getValue() ? "true" : "false",
                'type' => static::BOOLEAN_EVENT_CONDITION_TYPE,
            ],
            StringEventCondition::class => fn(StringEventCondition $eventCondition) => 
            [
                'key' => $eventCondition->getKey(),
                'value' => $eventCondition->getValue(),
                'type' => static::STRING_EVENT_CONDITION_TYPE,
            ],
        ];

        foreach ($eventConditionList as $eventCondition) {
            if (isset($types[$eventCondition::class])) {
                $source[] = $types[$eventCondition::class]($eventCondition);
            }
            
        }

        return $source;

    }

    public function mapEventValueListToSource(EventValueList $eventValueList): array
    {
        $source = [];

        $types =
        [
            IntegerEventValue::class => fn(IntegerEventValue $eventValue) => 
            [
                'key' => $eventValue->getKey(),
                'value' => (string)$eventValue->getValue(),
                'type' => static::INTEGER_EVENT_VALUE_TYPE,
            ],

            BooleanEventValue::class => fn(BooleanEventValue $eventValue) => 
            [
                'key' => $eventValue->getKey(),
                'value' => true === $eventValue->getValue() ? "true" : "false",
                'type' => static::BOOLEAN_EVENT_VALUE_TYPE,
            ],

            StringEventValue::class => fn(StringEventValue $eventValue) => 
            [
                'key' => $eventValue->getKey(),
                'value' => $eventValue->getValue(),
                'type' => static::STRING_EVENT_VALUE_TYPE,
            ],
        ];

        foreach ($eventValueList as $eventValue) {
            if (isset($types[$eventValue::class])) {
                $source[] = $types[$eventValue::class]($eventValue);
            }
        }

        return $source;


    }

    public function mapEventConditionListFromSource(array $source_list): EventConditionList
    {
        $errors = $this->getEventConditionListSourceErrors($source_list);
        if (count($errors) > 0) {
            $errors_msg = implode("\n", $errors);
            throw new EventSchemaInvalidException($errors_msg);
        }

        $eventConditionList = EventConditionList::makeInstance();

        $types =
        [
            static::INTEGER_EVENT_CONDITION_TYPE => function(array $event_condition_params): IntegerEventCondition {
                return IntegerEventCondition::makeInstance($event_condition_params['key'], (int)$event_condition_params['value']);
            },
            static::BOOLEAN_EVENT_CONDITION_TYPE => function(array $event_condition_params): BooleanEventCondition {
                $value = null;

                if ("true" === $event_condition_params['value']) {
                    $value = true;
                }
                else if ("false" === $event_condition_params['value']) {
                    $value = false;
                }

                return BooleanEventCondition::makeInstance($event_condition_params['key'], $value);
                
            },
            static::STRING_EVENT_CONDITION_TYPE => function(array $event_condition_params): StringEventCondition {
                return StringEventCondition::makeInstance($event_condition_params['key'], $event_condition_params['value']);
            }
        ];

        foreach ($source_list as $event_condition_params) {
            if (isset($types[$event_condition_params['type']])) {
                $eventCondition = $types[$event_condition_params['type']]($event_condition_params);
                $eventConditionList->push($eventCondition);
            }
        }

        return $eventConditionList;

    }

    public function mapEventValueListFromSource(array $source_list): EventValueList
    {
        $errors = $this->getEventValueListSourceErrors($source_list);
        if (count($errors) > 0) {
            $errors_msg = implode("\n", $errors);
            throw new EventSchemaInvalidException($errors_msg);
        }

        $eventValueList = EventValueList::makeInstance();

        $types = 
        [
            static::INTEGER_EVENT_VALUE_TYPE => function(array $event_value_params): IntegerEventValue {
                return IntegerEventValue::makeInstance($event_value_params['key'], $event_value_params['value']);
            },
            static::BOOLEAN_EVENT_VALUE_TYPE => function(array $event_value_params): BooleanEventValue {
                $value = null;

                if ("true" === $event_value_params['value']) {
                    $value = true;
                }
                else if ("false" === $event_value_params['value']) {
                    $value = false;
                }

                return BooleanEventValue::makeInstance($event_value_params['key'], $value);
            },
            static::STRING_EVENT_VALUE_TYPE => function(array $event_value_params): StringEventValue {
                return StringEventValue::makeInstance($event_value_params['key'], $event_value_params['value']);
            }
        ];

        foreach ($source_list as $event_value_params) {
            if (isset($types[$event_value_params['type']])) {
                $eventValue = $types[$event_value_params['type']]($event_value_params);
                $eventValueList->push($eventValue);
            }
        }

        return $eventValueList;

    }


    protected function getSchemaErrors(array $event_params)
    {
        $errors = [];

        if (!isset($event_params['event_uid'])) {
            $errors[] = "Идентификатор события не найден";
        }
        if (!isset($event_params['event_priority'])) {
            $errors[] = "Приоритет события не найден";
        }

        if (!isset($event_params['event_condition_list'])) {
            $errors[] = "Список условий возникновения событий не найден";
        }

        if (!isset($event_params['event_value_list'])) {
            $errors[] = "Список значений события не найден";
        }
        
        if (!is_array($event_params['event_condition_list'])) {
            $errors[] = "Список условий возникновения событий должен быть массивом";
        }

        if (!is_array($event_params['event_value_list'])) {
            $errors[] = "Список значений событий должен быть массивом";
        }

        $event_condition_list_errors = $this->getEventConditionListSourceErrors($event_params['event_condition_list']);
        if (count($event_condition_list_errors) > 0) {
            $event_condition_list_errors_msg = implode("\n", $event_condition_list_errors);
            $errors[] = $event_condition_list_errors_msg;
        }

        $event_value_list_errors = $this->getEventValueListSourceErrors($event_params['event_value_list']);
        if (count($event_value_list_errors) > 0) {
            $event_value_list_errors_msg = implode("\n", $event_value_list_errors);
            $errors[] = $event_value_list_errors_msg;
        }

        return $errors;

    }


    protected function getEventConditionListSourceErrors(array $event_condition_list): array
    {   
        $errors = [];
        foreach ($event_condition_list as $index => $event_condition_params) {
            $current_errors = [];

            if (!is_array($event_condition_params)) {
                $current_errors[] = "Условия возникновения события по индексу {$index} должно быть хэшем";
            }

            if (!isset($event_condition_params['key'])) {
                $current_errors[] = "Условие возникновения события по индексу {$index} не имеет ключа key";
            }

            if (!isset($event_condition_params['value'])) {
                $current_errors[] = "Условие возникновения события по индексу {$index} не имеет ключа value";
            }

            if (!isset($event_condition_params['type'])) {
                $current_errors[] = "Условие возникновения события по индексу {$index} не имеет ключа type";
            }

            if (!is_string($event_condition_params['key'])) {
                $current_errors[] = "Значение ключа key условия возникновения события по индексу {$index} должно иметь тип string";
            }

            if (!is_string($event_condition_params['value'])) {
                $current_errors[] = "Значение ключа value условия возникновения события по индексу {$index} должно иметь тип string";
            }

            if (!is_string($event_condition_params['type'])) {
                $current_errors[] = "Значение ключа type условия возникновения события по индексу {$index} должно иметь тип string";
            }

            if ($event_condition_params['type'] === static::BOOLEAN_EVENT_CONDITION_TYPE && 
            ("true" !== $event_condition_params['value'] && "false" !== $event_condition_params['value'])) {
                $current_errors[] = "Булево значение value условия возникновения события по индексу {$index} имеет неопределенное значение";
            }

            if (count($current_errors) > 0) {
                $current_errors_msg = implode(", ", $current_errors);
                $errors[] = $current_errors_msg;
            }

        }

        return $errors;

    }


    protected function getEventValueListSourceErrors(array $event_value_list): array
    {
        $errors = [];

        foreach ($event_value_list as $index => $event_value_params) {
            $current_errors = [];

            if (!is_array($event_value_params)) {
                $current_errors[] = "Значение события по индексу {$index} должно быть хэшем";
            }

            if (!isset($event_value_params['key'])) {
                $current_errors[] = "Значение события по индексу {$index} не имеет ключа key";
            }

            if (!isset($event_value_params['value'])) {
                $current_errors[] = "Значение события по индексу {$index} не имеет ключа value";
            }

            if (!isset($event_value_params['type'])) {
                $current_errors[] = "Значение события по индексу {$index} не имеет ключа type";
            }
            
            if (!is_string($event_value_params['key'])) {
                $current_errors[] = "Значение ключа key значения события по индексу {$index} должно иметь тип string";
            }
            if (!is_string($event_value_params['value'])) {
                $current_errors[] = "Значение ключа value значения события по индексу {$index} должно иметь тип string";
            }

            if (!is_string($event_value_params['type'])) {
                $current_errors[] = "Значение ключа type значения события по индексу {$index} должно иметь тип string";
            }

            if (static::BOOLEAN_EVENT_VALUE_TYPE === $event_value_params['type'] && 
            ("true" !== $event_value_params['value'] && "false" !== $event_value_params['value'])) {
                $current_errors[] = "Булево значение value значения события по индексу {$index} имеет неопределенное значение";
            }

            if (count($current_errors) > 0) {
                $current_errors_msg = implode(", ", $current_errors);
                $errors[] = $current_errors_msg;
            }
        }

        return $errors;
    }


}