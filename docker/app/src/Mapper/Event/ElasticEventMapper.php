<?php
declare(strict_types=1);
namespace App\Mapper\Event;
use App\Entity\Event;
use App\Factory\ValueObject\EventConditionListFactory;
use App\Factory\ValueObject\EventValueListFactory;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventValueListNotSpecifiedException;
use App\Exception\Value\UndefinedValueException;
use App\Exception\Value\ValueMustBeStringException;
use App\Exception\Value\ValueMustBeIntegerException;
use App\Exception\Value\ValueMustBeArrayException;

class ElasticEventMapper
{
    private EventConditionListFactory $eventConditionListFactory;
    private EventValueListFactory $eventValueListFactory;

    public function __construct(EventConditionListFactory $eventConditionListFactory, EventValueListFactory $eventValueListFactory)
    {
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }

    public function makeSourceFromEntity(Event $event)
    {

    }

    public function makeEntityFromSource(array $params)
    {
        $prepared_params = [];

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

        $this->checkEventConditionParamsListIsNotArray($params['event_condition_list']);
        $this->checkEventValueParamsListIsNotArray($params['event_value_list']);
        $this->checkEventConditionParamsListIsUndefined($params['event_condition_lis']);
        $this->checkEventValueParamsListIsUndefined($params['event_value_list']);



    }


        



    private function checkEventConditionParamsListIsNotArray(array $event_condition_params_list): void
    {
        $is_not_array_event_condition_params_list = array_filter($event_condition_params_list, fn(mixed $value) => !is_array($value));
        if (count($is_not_array_event_condition_params_list) > 0) {
            $is_not_array_event_condition_params_list_msg = implode("\n", $is_not_array_event_condition_params_list);
            throw new ValueMustBeArrayException("Следующие значения списка условий события не являются массивом: {$is_not_array_event_condition_params_list_msg}");
        }
    }

    private function checkEventValueParamsListIsNotArray(array $event_value_params_list): void
    {
        $is_not_array_event_value_params_list = array_filter($event_value_params_list, fn(mixed $value) => !is_array($value));
        if (count($is_not_array_event_value_params_list) > 0) {
            $is_not_array_event_value_params_list_msg = implode("\n", $is_not_array_event_value_params_list);
            throw new ValueMustBeArrayException("Следующие значения списка параметров события не являются массивом: {$is_not_array_event_value_params_list_msg}");
        }
    }

    private function checkEventConditionParamsListIsUndefined(array $event_condition_params_list): void
    {
        $undefined_event_condition_params_list = array_filter($event_condition_params_list, fn(array $value) => 
        !isset($value['key']) || !isset($value['value']) || !isset($value['type']));
        if (count($undefined_event_condition_params_list) > 0) {
            $prepared_undefined_event_condition_params_list = [];
            foreach ($undefined_event_condition_params_list as $undefined_param) {
                $keys = array_keys($undefined_param);
                $current = [];
                foreach ($keys as $key) {
                    $current[] = "$key => {$undefined_event_condition_params_list[$key]}";
                }
                $prepared_undefined_event_condition_params_list[] = implode("\n", $current);

                $message = implode("\n\n", $prepared_undefined_event_condition_params_list);
                throw new UndefinedValueException("Значение условия события должно иметь ключи key, value, type. Обнаружены следующие значения: $message");

            }
            
        }
    }

    private function checkEventValueParamsListIsUndefined(array $event_value_params_list): void
    {
        $undefined_event_value_params_list = array_filter($event_value_params_list, fn(mixed $value) => 
        !isset($value['key']) || !isset($value['value']) || !isset($value['type']));
        if (count($undefined_event_value_params_list) > 0) {
            $prepared_undefined_event_value_params_list = [];
            foreach ($undefined_event_value_params_list as $undefined_param) {
                $keys = array_keys($undefined_param);
                $current = [];
                foreach ($keys as $key) {
                    $current[] = "$key => {$undefined_event_value_params_list[$key]}";
                }
                $prepared_undefined_event_value_params_list[] = implode("\n", $current);
                $message = implode("\n\n", $prepared_undefined_event_value_params_list);
                throw new UndefinedValueException("Значение параметра события должно иметь ключи key, value, type. Обнаружены следующие значения: $message");
            }
        }
    }

    private function checkEventConditionListOnUntyped(array $event_condition_params_list): void
    {   
        $errors = [];
        $checks =
        [
            'key' => 
            [
                'check' => fn(mixed $value) => is_string($value),
                'message' => 'Параметр key не является строковым',
            ],
            'value' =>
            [
                'check' => fn(mixed $value) => is_string($value),
                'message' => 'Параметр value не является строковым',
            ],
            'type' => 
            [
                'check' => fn(mixed $value) => is_string($value),
                'message' => 'Параметр type не является строковым',
            ],
        ];

        

    }



}