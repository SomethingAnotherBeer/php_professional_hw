<?php
declare(strict_types=1);
namespace App\Service;

use App\DTO\EventConditionListDTO;
use App\DTO\EventDTO;
use App\Entity\Event;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventNotFoundException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventValueNotSpecifiedException;
use App\Factory\EventContainerFactory;
use App\Repository\Event\EventRepositoryInterface;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventContainerList;
use App\ValueObject\Event\EventPriorityValue;
use App\ValueObject\Event\EventUidValue;
use App\ValueObject\Event\EventValue;

class EventService
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEvent(EventDTO $eventDTO): EventContainer
    {
        if (null === $eventDTO->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
        $event = $this->getEventByUidWithThrow($eventDTO->getUid());

        return EventContainerFactory::makeInstanceFromEntity($event);
    }

    public function getEventsByEventConditionList(EventConditionListDTO $eventConditionListDTO): EventContainerList
    {
        if (count($eventConditionListDTO->asArray()) === 0) {
            throw new EventConditionListNotSpecifiedException("Список условий события не может быть пустым");
        }
        $eventConditionList = EventConditionList::makeInstance($eventConditionListDTO->asArray());

        /** @var Event[] $event_condition_list */
        $event_list = $this->eventRepository->getEventsByConditionList($eventConditionList);
        
        return EventContainerFactory::makeInstanceListFromEntityList($event_list);


    }

    public function createEvent(EventDTO $eventDTO): EventContainer
    {
        if (null === $eventDTO->getPriority()) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (null === $eventDTO->getEventConditionList() || count($eventDTO->getEventConditionList()->asArray()) === 0) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий события");
        }

        if (null === $eventDTO->getEventValue() || count($eventDTO->getEventValue()->asArray()) === 0) {
            throw new EventValueNotSpecifiedException("Параметры события не указаны");
        }

        $event_uid = $eventDTO->getUid() ?? $this->generateEventUid();


        $eventUidValue = EventUidValue::makeInstance($event_uid);
        $eventPriorityValue = EventPriorityValue::makeInstance($eventDTO->getPriority());
        $eventConditionList = EventConditionList::makeInstance($eventDTO->getEventConditionList()->asArray());
        $eventValue = EventValue::makeInstance($eventDTO->getEventValue()->asArray());

        $event = Event::makeInstance($eventUidValue, $eventPriorityValue, $eventConditionList, $eventValue);
        $event = $this->eventRepository->flushEvent($event);

        return EventContainerFactory::makeInstanceFromEntity($event);
    }

    public function changeEvent(EventDTO $eventDTO): EventContainer
    {
        if (null === $eventDTO->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
        $event = $this->eventRepository->getEvent($eventDTO->getUid());

        $currentEventContainer = EventContainerFactory::makeInstanceFromDTO($eventDTO);

        if (null !== $currentEventContainer->getEventPriority() 
            && $currentEventContainer->getEventPriority() !== $event->getEventPriority()) {

            $event->setEventPriority(EventPriorityValue::makeInstance($currentEventContainer->getEventPriority()));
        }

        if (null !== $currentEventContainer->getEventConditionList() 
            && $currentEventContainer->getEventConditionList()->asArray() !== $event->getEventConditionList()->asArray()) {
        
            $event->setEventConditionList(EventConditionList::makeInstance($eventDTO->getEventConditionList()->asArray()));
        }

        if (null !== $currentEventContainer->getEventValue()
            && $currentEventContainer->getEventValue()->asArray() !== $event->getEventValue()->asArray()) {
            
            $event->setEventValue(EventValue::makeInstance($eventDTO->getEventValue()->asArray()));
        }

        $event = $this->eventRepository->flushEvent($event);

        return EventContainerFactory::makeInstanceFromEntity($event);


    }

    public function removeEvent(EventDTO $eventDTO): EventContainer
    {
        if (null === $eventDTO->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }

        $event = $this->eventRepository->getEvent($eventDTO->getUid());
        $event = $this->eventRepository->removeEvent($event);
        
        return EventContainerFactory::makeInstanceFromEntity($event);
    }

    public function getEventsByConditionList(EventConditionListDTO $eventConditionListDTO): EventContainerList
    {
        if (count($eventConditionListDTO->asArray()) === 0) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий события");
        }

        $eventConditionList = EventConditionList::makeInstance($eventConditionListDTO->asArray());
        /** @var Event[] $event_list */
        $event_list = $this->eventRepository->getEventsByConditionList($eventConditionList);

        return EventContainerFactory::makeInstanceListFromEntityList($event_list);
    }

    public function clearEvents(): EventContainerList
    {   
        /** @var Event[] $event_list */
        $event_list = $this->eventRepository->clearEvents();

        return EventContainerFactory::makeInstanceListFromEntityList($event_list);

    }

    protected function getEventByUidWithThrow(string $uid): Event
    {
        $event = $this->eventRepository->getEvent($uid);
        if (!$event) {
            throw new EventNotFoundException("Событие не найдено");
        }

        return $event;
    }

    protected function generateEventUid(): string
    {
        $uid_str = $this->generateEventUidString();

        while (null !== $this->eventRepository->getEventOrNull($uid_str)) {
            $uid_str = $this->generateEventUidString();
        }

        return $uid_str;
    }



    protected function generateEventUidString(): string
    {
        $uid_len = 20;
        $uid_chars = [];
        
        for ($i = 0; $i < $uid_len; $i++) {
            if ($i % 2 === 0) {
                $uid_chars = chr(rand(97, 122));
            }
            else {
                $uid_chars[] = (string)rand(0, 9);
            }
        }

        $uid_chars_str = implode('', $uid_chars);

        return $uid_chars_str;
    }



}