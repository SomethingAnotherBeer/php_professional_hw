<?php
declare(strict_types=1);
namespace App\Factory;

use App\DTO\EventDTO;
use App\Entity\Event;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventValueNotSpecifiedException;
use App\Exception\Value\ValueMustBeArrayException;
use App\Exception\Value\ValueMustBeIntegerException;
use App\Exception\Value\ValueMustBeStringException;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventValue;

class EventFactory
{
    public static function makeInstanceFromDTO(EventDTO $eventDTO): Event
    {
        $eventUidValue = null;
        $eventPriorityValue = null;
        $eventConditionList = null;
        $eventValue = null;

        if (null === $eventDTO->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }

        if (null === $eventDTO->getPriority()) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (null === $eventDTO->getEventConditionList()) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий события");
        }

        if (null === $eventDTO->getEventValue()) {
            throw new EventValueNotSpecifiedException("Не указано значение события");
        }

        $eventUidValue = EventUidValue::makeInstance($eventDTO->getUid());
        $eventPriorityValue = EventPriorityValue::makeInstance($eventDTO->getPriority());
        $eventConditionList = EventConditionList::makeInstance($eventDTO->getEventConditionList()->asArray());
        $eventValue = EventValue::makeInstance($eventDTO->getEventValue()->asArray());


        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);

    }

    public static function makeInstanceFromEventContainer(EventContainer $eventContainer): Event
    {
        $event_uid_value = $eventContainer->getEventUid();
        $event_priority_value = $eventContainer->getEventPriority();
        $eventConditionList = $eventContainer->getEventConditionList();
        $eventValue = $eventContainer->getEventValue();

        if (null === $event_uid_value) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }

        if (null === $event_priority_value) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (null === $eventConditionList) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий события");
        }

        if (null === $eventValue) {
            throw new EventValueNotSpecifiedException("Не указано значение события");
        }

        $eventUidValue = EventUidValue::makeInstance($event_uid_value);
        $eventPriorityValue = EventPriorityValue::makeInstance($event_priority_value);

        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);

    }

    public static function makeInstanceFromSource(array $event_params): Event
    {
        if (!isset($event_params['uid'])) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }

        if (!isset($event_params['priority'])) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (!isset($event_params['event_condition_list'])) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий события");
        }

        if (!isset($event_params['event_value'])) {
            throw new EventValueNotSpecifiedException("Не указано значение события");
        }

        if (!is_string($event_params['uid'])) {
            throw new ValueMustBeStringException("Значение идентификатора события должно быть строкой");
        }

        if (!is_int($event_params['priority'])) {
            throw new ValueMustBeIntegerException("Значение приоритета события должно быть целым числом");
        }

        if (!is_array($event_params['event_condition_list'])) {
            throw new ValueMustBeArrayException("Значение списка условий возникновения события должно быть массивом");
        }

        if (!is_array($event_params['event_value'])) {
            throw new ValueMustBeArrayException("Значение параметров события должно быть массивом");
        }

        $eventUidValue = EventUidValue::makeInstance($event_params['uid']);
        $eventPriorityValue = EventPriorityValue::makeInstance($event_params['priority']);
        $eventConditionList = EventConditionList::makeInstance($event_params['event_condition_list']);
        $eventValue = EventValue::makeInstance($event_params['event_value']);

        return Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);


    }




}