<?php
declare(strict_types=1);
namespace App\Factory\ValueObject;

use App\Exception\Value\ValueMustBeBooleanException;
use App\Exception\Value\ValueMustBeIntegerException;
use App\Exception\Value\ValueMustBeStringException;
use App\Exception\Value\ValueMustNotBeEmptyException;
use App\ValueObject\Event\BooleanEventConditionList;
use App\ValueObject\Event\IntegerEventConditionList;
use App\ValueObject\Event\IntegerEventConditionValue;
use App\ValueObject\Event\BooleanEventConditionValue;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\StringEventConditionList;
use App\ValueObject\Event\StringEventConditionValue;

class EventConditionListFactory
{
    public function makeEventConditionList(array $params): EventConditionList
    {
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Не указан список условий события");
        }
        $this->checkKeys($params);

        $integer_params = array_filter($params, fn(mixed $value) => is_int($value));
        $boolean_params = array_filter($params, fn(mixed $value) => is_bool($value));
        $string_params = array_filter($params, fn(mixed $value) => is_string($value));

        $eventConditionList = EventConditionList::makeInstance();

        if (count($integer_params) > 0) {
            $eventConditionList->setIntegerEventConditionList($this->makeIntegerEventConditionList($integer_params));
        }

        if (count($boolean_params) > 0) {
            $eventConditionList->setBooleanEventConditionList($this->makeBooleanEventConditionList($boolean_params));
        }

        if (count($string_params) > 0) {
            $eventConditionList->setStringEventConditionList($this->makeStringEventConditionList($string_params));
        }

        return $eventConditionList;

    }

    public function makeIntegerEventConditionListWithCheck(array $params): IntegerEventConditionList
    {
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Целочисленный список условий события не указан");
        }

        $this->checkKeys($params);

        $unexpected_params = array_filter($params, fn(mixed $value) => !is_int($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeIntegerException("Следующие значения списка условий события не являются целочисленными значениями: {$unexpected_params_msg}");
        }

        return $this->makeIntegerEventConditionList($params);


    }

    public function makeBooleanEventConditionListWithCheck(array $params): BooleanEventConditionList
    {
         if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Булевый список условий события не указан");
        }

        $this->checkKeys($params);

        $unexpected_params = array_filter($params, fn(mixed $value) => !is_bool($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeBooleanException("Следующие значнения списка условий события не являются булевыми значениями: {$unexpected_params_msg}");
        }

        return $this->makeBooleanEventConditionList($params);

    }

    public function makeStringEventConditionListWithCheck(array $params): StringEventConditionList
    {
         if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Строковый список условий события не указан");
        }

        $this->checkKeys($params);

        $unexpected_params = array_filter($params, fn(mixed $value) => !is_string($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeStringException($unexpected_params_msg);
        }

        return $this->makeStringEventConditionList($params);
    }

    protected function makeIntegerEventConditionList(array $params): IntegerEventConditionList
    {       
        /** @var IntegerEventConditionValue[] $integer_event_condition_list */
        $integer_event_condition_list = [];
        foreach ($params as $param_key => $param_value) {
            $integer_event_condition_list[] = IntegerEventConditionValue::makeInstance($param_key, $param_value);
        }

        return IntegerEventConditionList::makeInstance($integer_event_condition_list);

    }

    protected function makeBooleanEventConditionList(array $params): BooleanEventConditionList
    {   
        /** @var BooleanEventConditionValue[] $boolean_event_condition_list */
        $boolean_event_condition_list = [];

        foreach ($params as $param_key => $param_value) {
            $boolean_event_condition_list[] = BooleanEventConditionValue::makeInstance($param_key, $param_value);
        }

        return BooleanEventConditionList::makeInstance($boolean_event_condition_list);
    }

    protected function makeStringEventConditionList(array $params): StringEventConditionList
    {
        /** @var StringEventConditionValue[] $string_event_condition_list */
        $string_event_condition_list = [];
        
        foreach ($params as $param_key => $param_value) {
            $string_event_condition_list[] = StringEventConditionValue::makeInstance($param_key, $param_value);
        }

        return StringEventConditionList::makeInstance($string_event_condition_list);
    }

    protected function checkKeys(array $params): void
    {
        $unexpected_keys = array_filter($params, fn(string|int $key) => !is_string($key), ARRAY_FILTER_USE_KEY);
        if (count($unexpected_keys) > 0) {
            $unexpected_keys_msg = implode("\n", $unexpected_keys);
            throw new ValueMustBeStringException("Следующие ключи в списке условий события не являются строковыми ключами: {$unexpected_keys_msg}");
        }

    }

    


}