<?php
declare(strict_types=1);
namespace App\Service;

use App\DTO\Event\EventConditionListDTO;
use App\DTO\Event\EventDTO;
use App\Exception\Event\EventNotFoundException;
use App\Factory\Model\Event\EventFactory;
use App\Model\Event\Event;
use App\Entity\Event\Event as EventEntity;
use App\Exception\Event\EventAlreadyExistsException;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventValueListNotSpecifiedException;
use App\Factory\Model\Event\EventConditionListFactory;
use App\Factory\Model\Event\EventListFactory;
use App\Factory\Model\Event\EventValueListFactory;
use App\Model\Event\EventCondition\EventConditionInterface;
use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventList;
use App\Model\Event\EventPriority;
use App\Model\Event\EventUid;
use App\Model\Event\EventValue\EventValueInterface;
use App\Model\Event\EventValue\EventValueList;
use App\Model\Message\Message;
use App\Repository\EventRepositoryInterface;

class EventService
{
    private EventRepositoryInterface $eventRepository;
    private EventListFactory $eventListFactory;
    private EventFactory $eventFactory;
    private EventConditionListFactory $eventConditionListFactory;
    private EventValueListFactory $eventValueListFactory;


    public function __construct
    (
        EventRepositoryInterface $eventRepository,
        EventListFactory $eventListFactory,
        EventFactory $eventFactory,
        EventConditionListFactory $eventConditionListFactory,
        EventValueListFactory $eventValueListFactory)
    {
        $this->eventRepository = $eventRepository;
        $this->eventListFactory = $eventListFactory;
        $this->eventFactory = $eventFactory;
        $this->eventConditionListFactory = $eventConditionListFactory;
        $this->eventValueListFactory = $eventValueListFactory;
    }

    public function getEvent(EventDTO $eventDTO): Event
    {
        if (null === $eventDTO->getEventUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
        $event = $this->getEventByUidWithThrow($eventDTO->getEventUid());
        return $this->eventFactory->makeEventFromEntity($event);
    }

    public function createEvent(EventDTO $eventDTO): Event
    {
        $eventUid = null;
        $eventPriority = null;
        $eventConditionList = null;
        $eventValueList = null;

        if (null === $eventDTO->getEventUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }

        if (null === $eventDTO->getEventPriority()) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (null === $eventDTO->getEventConditionListDTO()) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий возникновения события");
        }

        if (null === $eventDTO->getEventValueListDTO()) {
            throw new EventValueListNotSpecifiedException("Не указан список значений события");
        }
        
        if ($this->eventRepository->getEventByUid($eventDTO->getEventUid())) {
            throw new EventAlreadyExistsException("Событие с таким идентификатором уже существует в системе");
        }
        
        $eventUid = EventUid::makeInstance($eventDTO->getEventUid());
        $eventPriority = EventPriority::makeInstance($eventDTO->getEventPriority());
        $eventConditionList = $this->eventConditionListFactory->makeFromDTO($eventDTO->getEventConditionListDTO());
        $eventValueList = $this->eventValueListFactory->makeFromDTO($eventDTO->getEventValueListDTO());

        $eventEntity = EventEntity::makeInstance($eventUid, $eventPriority, $eventConditionList, $eventValueList);
        $this->eventRepository->createEvent($eventEntity);

        return $this->eventFactory->makeEventFromEntity($eventEntity);
        
       
    }

    public function changeEvent(EventDTO $eventDTO): Event
    {
        $currentEvent = $this->eventFactory->makeEventFromDTO($eventDTO);
        if (null === $currentEvent->getEventUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
        $eventEntity = $this->getEventByUidWithThrow($eventDTO->getEventUid());

        if (null !== $currentEvent->getEventPriority() && $currentEvent->getEventPriority() !== $eventEntity->getEventPriority()) {
            $eventEntity->setEventPriority(EventPriority::makeInstance($currentEvent->getEventPriority()));
        }

        if (null !== $currentEvent->getEventConditionList() && !$this->compareEventConditionListsIsEquals($currentEvent->getEventConditionList(), $eventEntity->getEventConditionList())) {
            $eventEntity->setEventConditionList($currentEvent->getEventConditionList());
        }

        if (null !== $currentEvent->getEventValueList() && !$this->compareEventValueListsIsEquals($currentEvent->getEventValueList(), $eventEntity->getEventValueList())) {
            $eventEntity->setEventValueList($currentEvent->getEventValueList());
        }

        $this->eventRepository->changeEvent($eventEntity);
        return $this->eventFactory->makeEventFromEntity($eventEntity);

    }

    public function removeEvent(EventDTO $eventDTO): Event
    {
        if (null === $eventDTO->getEventUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
        $event = $this->getEventByUidWithThrow($eventDTO->getEventUid());
        $this->eventRepository->removeEvent($event);

        return $this->eventFactory->makeEventFromEntity($event);

        
    }

    public function getEventsByConditionList(EventConditionListDTO $eventConditionListDTO): EventList
    {
        $eventConditionList = $this->eventConditionListFactory->makeFromDTO($eventConditionListDTO);
        
        $event_list = $this->eventRepository->findEventsByConditionList($eventConditionList);
        $event_list = $this->extractEventListWithMaxPriority($event_list);



        return $this->eventListFactory->makeEventListFromEntityList($event_list);
    }

    public function clearEvents(): Message
    {
        $removed_count = $this->eventRepository->clearEvents();
        $message = Message::makeInstance("Успешно удалено $removed_count событий", true);

        return $message;
    }

    protected function getEventByUidWithThrow(string $event_uid): EventEntity
    {
        $event = $this->eventRepository->getEventByUid($event_uid);
        if (!$event) {
            throw new EventNotFoundException("Событие не найдено");
        }
        return $event;
    }

    protected function compareEventConditionListsIsEquals(EventConditionList $firstEventConditionList, EventConditionList $secondEventConditionList): bool
    {
        $first_list = $firstEventConditionList->all();
        $second_list = $secondEventConditionList->all();
        $is_equals = true;

        if (count($first_list) !== count($second_list)) {
            $is_equals = false;
            return $is_equals;
        }

        usort($first_list, fn(EventConditionInterface $a, EventConditionInterface $b) => $a->getKey() <=> $b->getKey());
        usort($second_list, fn(EventConditionInterface $a, EventConditionInterface $b) => $a->getKey() <=> $b->getKey());

        for ($i = 0; $i < count($first_list); $i++) {
            if ($first_list[$i]->getKey() !== $second_list[$i]->getKey() 
                || $first_list[$i]->getValue() !== $second_list[$i]->getValue()) {
                $is_equals = false;
                break;
            }
        }

        return $is_equals;

    }

    protected function compareEventValueListsIsEquals(EventValueList $firstEventValueList, EventValueList $secondEventValueList): bool
    {
        $first_list = $firstEventValueList->all();
        $second_list = $secondEventValueList->all();
        $is_equals = true;

         if (count($first_list) !== count($second_list)) {
            $is_equals = false;
            return $is_equals;
        }

        usort($first_list, fn(EventValueInterface $a, EventValueInterface $b) => $a->getKey() <=> $b->getKey());
        usort($second_list, fn(EventValueInterface $a, EventValueInterface $b) => $a->getKey() <=> $b->getKey());

        for ($i = 0; $i < count($first_list); $i++) {
            if ($first_list[$i]->getKey() !== $second_list[$i]->getKey() 
                || $first_list[$i]->getValue() !== $second_list[$i]->getValue()) {
                $is_equals = false;
                break;
            }
        }

        return $is_equals;


    }

    /**
     * @param EventEntity[] $event_list
     * @return EventEntity[]
     */
    protected function extractEventListWithMaxPriority(array $event_list): array
    {
        $max_priorirty = 0;
        foreach ($event_list as $event) {
            if ($event->getEventPriority() > $max_priorirty) {
                $max_priorirty = $event->getEventPriority();
            }
        }
        $event_list = array_filter($event_list, fn(EventEntity $event) => $event->getEventPriority() === $max_priorirty);

        return $event_list;

    }


}