<?php
declare(strict_types=1);
namespace App\ValueObject\Event;


class EventConditionList
{
    private IntegerEventConditionList $integerEventConditionList;
    private BooleanEventConditionList $booleanEventConditionList;
    private StringEventConditionList $stringEventConditionList;

    public static function makeInstance(): EventConditionList
    {
        return new EventConditionList();
    }


    public function setIntegerEventConditionList(IntegerEventConditionList $integerEventConditionList): static
    {
        $this->integerEventConditionList = $integerEventConditionList;
        return $this;
    }

    public function getIntegerEventConditionList(): IntegerEventConditionList
    {
        return $this->integerEventConditionList;
    }

    public function setBooleanEventConditionList(BooleanEventConditionList $booleanEventConditionList): static
    {
        $this->booleanEventConditionList = $booleanEventConditionList;
        return $this;
    }

    public function getBooleanEventConditionList(): BooleanEventConditionList
    {
        return $this->booleanEventConditionList;
    }

    public function setStringEventConditionList(StringEventConditionList $stringEventConditionList): static
    {
        $this->stringEventConditionList = $stringEventConditionList;

        return $this;
    }

    public function getStringEventConditionList(): StringEventConditionList
    {
        return $this->stringEventConditionList;
    }

    /**
     * @return EventConditionValueInterface[]
     */
    public function all(): array
    {
        return array_merge($this->integerEventConditionList->all(), $this->booleanEventConditionList->all(), $this->stringEventConditionList->all());
    }

    public function asArray(): array
    {
        $integer_event_condition_params = $this->integerEventConditionList->asArray();
        $boolean_event_condition_params = $this->booleanEventConditionList->asArray();
        $string_event_condition_params = $this->stringEventConditionList->asArray();

        return array_merge($integer_event_condition_params, $boolean_event_condition_params, $string_event_condition_params);
    }



}