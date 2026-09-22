<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class EventValueList
{
    private IntegerEventValueList $integerEventValueList;
    private BooleanEventValueList $booleanEventValueList;
    private StringEventValueList $stringEventValueList;

    
    public static function makeInstance(): EventValueList
    {
        return new EventValueList();
    }

    public function getIntegerEventValueList(): IntegerEventValueList
    {
        return $this->integerEventValueList;
    }

    public function setIntegerEventValueList(IntegerEventValueList $integerEventValueList): static
    {
        $this->integerEventValueList = $integerEventValueList;
        return $this;
    }

    public function getBooleanEventValueList(): BooleanEventValueList
    {
        return $this->booleanEventValueList;
    }

    public function setBooleanEventValueList(BooleanEventValueList $booleanEventValueList): static
    {
        $this->booleanEventValueList = $booleanEventValueList;
        return $this;
    }

    public function getStringEventValueList(): StringEventValueList
    {
        return $this->stringEventValueList;
    }

    public function setStringEventValueList(StringEventValueList $stringEventValueList)
    {
        $this->stringEventValueList = $stringEventValueList;
        return $this;
    }

    public function asArray(): array
    {
        $integer_event_value_params = $this->integerEventValueList->asArray();
        $boolean_event_value_params = $this->booleanEventValueList->asArray();
        $string_event_value_params = $this->stringEventValueList->asArray();

        return array_merge($integer_event_value_params, $boolean_event_value_params, $string_event_value_params);
        
    }

}