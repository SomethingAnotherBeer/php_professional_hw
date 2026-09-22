<?php
declare(strict_types=1);
namespace App\Factory\ValueObject;

use App\Exception\Value\ValueMustBeBooleanException;
use App\Exception\Value\ValueMustBeStringException;
use App\Exception\Value\ValueMustNotBeEmptyException;
use App\ValueObject\Event\IntegerEventValue;
use App\ValueObject\Event\BooleanEventValue;
use App\ValueObject\Event\BooleanEventValueList;
use App\ValueObject\Event\EventValueList;
use App\ValueObject\Event\IntegerEventValueList;
use App\ValueObject\Event\StringEventValue;
use App\ValueObject\Event\StringEventValueList;

class EventValueListFactory
{
    public function makeEventValueList(array $params): EventValueList
    {   
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Не указан список параметров события");
        }
        $this->checkKeys($params);

        $eventValueList = EventValueList::makeInstance();

        $integer_params = array_filter($params, fn(mixed $value) => is_int($value));
        $boolean_params = array_filter($params, fn(mixed $value) => is_bool($value));
        $string_params = array_filter($params, fn(mixed $value) => is_string($value));

        if (count($integer_params) > 0) {
            $eventValueList->setIntegerEventValueList($this->makeIntegerEventValueList($integer_params));
        }

        if (count($boolean_params) > 0){
            $eventValueList->setBooleanEventValueList($this->makeBooleanEventValueList($boolean_params));
        }

        if (count($string_params) > 0) {
            $eventValueList->setStringEventValueList($this->makeStringEventValueList($string_params));
        }

        return $eventValueList;
        
    }

    public function makeIntegerEventValueListWithCheck(array $params): IntegerEventValueList
    {
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Не указан список целочисленных параметров события");
        }

        $this->checkKeys($params);
        $unexpected_params = array_filter($params, fn(mixed $value) => !is_int($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeStringException("Следующие значения в списке параметров события не являются целочисленными: {$unexpected_params_msg}");
        }

        return $this->makeIntegerEventValueList($params);

    }

    public function makeBooleanEventValueListWithCheck(array $params): BooleanEventValueList
    {
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Не указан список булевых параметров события");
        }

        $this->checkKeys($params);
        $unexpected_params = array_filter($params, fn(mixed $value) => !is_bool($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeBooleanException("Следующие значения в списке параметров события не являются булевыми: {$unexpected_params_msg}");
        }

        return $this->makeBooleanEventValueList($params);

    }

    public function makeStringEventValueListWithCheck(array $params): StringEventValueList
    {
        if (count($params) === 0) {
            throw new ValueMustNotBeEmptyException("Не указан список строковых параметров события");
        }

        $this->checkKeys($params);
        $unexpected_params = array_filter($params, fn(mixed $value) => !is_string($value));
        if (count($unexpected_params) > 0) {
            $unexpected_params_msg = implode("\n", $unexpected_params);
            throw new ValueMustBeStringException("Следующие значения в списке параметров события не являются строковыми: {$unexpected_params_msg}");
        }

        return $this->makeStringEventValueList($params);
    }

    protected function makeIntegerEventValueList(array $params): IntegerEventValueList
    {   
        /** @var IntegerEventValue[] $integer_event_value */
        $integer_event_value_list = [];

        foreach ($params as $param_key => $param_value) {
            $integer_event_value_list[] = IntegerEventValue::makeInstance($param_key, $param_value);
        }

        return IntegerEventValueList::makeInstance($integer_event_value_list);
    }

    protected function makeBooleanEventValueList(array $params): BooleanEventValueList
    {   
        /** @var BooleanEventValue[] $boolean_event_value_list */
        $boolean_event_value_list = [];

        foreach ($params as $param_key => $param_value) {
            $boolean_event_value_list[] = BooleanEventValue::makeInstance($param_key, $param_value);
        }

        return BooleanEventValueList::makeInstance($boolean_event_value_list);
    }

    protected function makeStringEventValueList(array $params): StringEventValueList
    {   
        /** @var StringEventValue[] $string_event_value_list */
        $string_event_value_list = [];

        foreach ($params as $param_key => $param_value) {
            $string_event_value_list[] = StringEventValue::makeInstance($param_key, $param_value);
        }

        return StringEventValueList::makeInstance($string_event_value_list);
    }

    protected function checkKeys(array $params): void
    {
        $unexpected_keys = array_filter($params, fn(string|int $key) => !is_string($key), ARRAY_FILTER_USE_KEY);
        if (count($unexpected_keys) > 0) {
            $unexpected_keys_msg = implode("\n", $unexpected_keys);
            throw new ValueMustBeStringException("Следующие ключи в списке параметров события не являются строковыми ключами: {$unexpected_keys_msg}");
        }

    }




}