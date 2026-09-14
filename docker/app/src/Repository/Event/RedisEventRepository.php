<?php
declare(strict_types=1);
namespace App\Repository\Event;

use App\Checker\EventChecker;
use App\Client\RedisClient;
use App\Exception\Event\EventInChangeProcessException;
use App\Exception\Event\EventNotFoundException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Factory\EventFactory;
use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventContainerList;

class RedisEventRepository implements EventRepositoryInterface
{
    public const EVENT_KEY = "event";
    public const PROCESS_CHANGE_KEY = "process_change";
    public const PROCESS_CHANGE_ON = "1";
    public const PROCESS_CHANGE_OFF = "0";
    public const READ_OFFSET = 100;

    private RedisClient $redisClient;
    private EventFactory $eventFactory;
    private EventChecker $eventChecker;

    public function __construct(RedisClient $redisClient, EventFactory $eventFactory, EventChecker $eventChecker)
    {
        $this->redisClient = $redisClient;
        $this->eventFactory = $eventFactory;
        $this->eventChecker = $eventChecker;
    }

    public function getEventsByConditionList(EventConditionList $eventConditionList): EventContainerList
    {
        $connection = $this->redisClient->getConnection();

        if ($this->checkEventsIsInChangeProcess()) {
            throw new EventInChangeProcessException("На данный момент данные событий изменяются, попробуйте позже");
        }

        /** @var EventContainer[] $coinciding_events */
        $coinciding_events = [];

        $coincidingEventContainerList = new EventContainerList();
        $offset = static::READ_OFFSET;

        while (($current_event_keys = $connection->scan($offset, static::EVENT_KEY, 100)) !== null) {
            $events_params = [];
            foreach ($current_event_keys as $current_event_key) {
                $event_params = $connection->get($current_event_key);
                $events_params[] = json_decode($event_params, true);
            }

            $eventContainerList = $this->eventFactory->makeEventContainerList($events_params);

            foreach ($eventContainerList as $eventContainer) {
                if ($eventContainer->getEventConditionList()->asArray() === $eventConditionList->asArray()) {
                    $coinciding_events[] = $eventContainer;
                }
            }
            $offset+= static::READ_OFFSET;

        }

        if (count($coinciding_events) > 0) {
            $event_max_priority = 0;
            foreach ($coinciding_events as $coincidingEvent) {
                if ($coincidingEvent->getPriority() > $event_max_priority) {
                    $event_max_priority = $coincidingEvent->getPriority();
                }
            }

            $coinciding_events = array_filter($coinciding_events, fn(EventContainer $eventContainer) => $eventContainer->getPriority() === $event_max_priority);
            
            foreach ($coinciding_events as $coincidingEvent) {
                $coincidingEventContainerList->pushEventContainer($coincidingEvent);
            }

        }

        return $coincidingEventContainerList;

    }   

    public function getEventByUid(string $uid): EventContainer
    {
        $event = $this->redisClient->getConnection()->get(static::EVENT_KEY . $uid);
        if (!$event) {
            throw new EventNotFoundException("Событие не найдено");
        }

        return $this->eventFactory->makeEventContainer($event);
    }

    public function createEvent(EventContainer $eventContainer): EventContainer
    {
        $this->eventChecker->checkEventForCreate($eventContainer);

        if (null === $eventContainer->getUid()) {
            $eventContainer->setUid($this->generateEventUid());
        }

        $event_params = $eventContainer->asArray([
            'priority' => true,
            'event_condition_list' => true,
            'event' => true,
        ]);

        $event_params = json_encode($event_params, JSON_UNESCAPED_UNICODE);

        $this->redisClient->getConnection()->set(static::EVENT_KEY . $eventContainer->getUid(), $event_params);

        return $eventContainer;


    }
        
    public function changeEvent(EventContainer $eventContainer): EventContainer
    {
        $this->eventChecker->checkEventForUpdate($eventContainer);

        $target_event_params = $this->redisClient->getConnection()->get($eventContainer->getUid());
        if (!$target_event_params) {
            throw new EventNotFoundException("Событие не найдено");
        }
        $target_event_params = json_decode($target_event_params, true);
        $targetEvent = $this->eventFactory->makeEventContainer($target_event_params);

        if ($eventContainer->getPriority() !== null && $eventContainer->getPriority() !== $targetEvent->getPriority()) {
            $targetEvent->setPriority($eventContainer->getPriority());
        }
        
        if ($eventContainer->getEventConditionList() !== null
            && $eventContainer->getEventConditionList()->asArray() !== $targetEvent->getEventConditionList()->asArray()) {
                $targetEvent->setEventConditionList($eventContainer->getEventConditionList());
            }

        if ($eventContainer->getEvent() !== null 
        && $eventContainer->getEvent()->asArray() !== $targetEvent->getEvent()->asArray()) {
            $targetEvent->setEvent($eventContainer->getEvent());
        }

        $target_event_params = json_encode($targetEvent->asArray(), JSON_UNESCAPED_UNICODE);
        $this->redisClient->getConnection()->set($targetEvent->getUid(), $target_event_params);

        return $targetEvent;


    }

    public function removeEvent(EventContainer $eventContainer)
    {   
        $this->eventChecker->checkEventForDelete($eventContainer);

        if (null === $this->redisClient->getConnection()->get(static::EVENT_KEY . $eventContainer->getUid())) {
            throw new EventNotFoundException("Событие не найдено");
        }

        $this->redisClient->getConnection()->del(static::EVENT_KEY . $eventContainer->getUid());
        return $eventContainer;
    }


    public function clearEvents(): EventContainerList
    {
        $connection = $this->redisClient->getConnection();

        if ($this->checkEventsIsInChangeProcess()) {
            throw new EventInChangeProcessException("На данный момент данные событий изменяются, попробуйте позже");
        }
        $connection->set(static::PROCESS_CHANGE_KEY, static::PROCESS_CHANGE_ON);

        $eventContainerList = new EventContainerList();

        $offset = static::READ_OFFSET;

        while (($current_event_keys = $connection->scan($offset, static::EVENT_KEY)) !== null) {
            $current_events_params = [];
            foreach ($current_event_keys as $current_event_key) {
                $current_event_params = $connection->get(static::EVENT_KEY . $current_event_key);
                $current_events_params[] = json_decode($current_event_params);

                $connection->del(static::EVENT_KEY . $current_event_key);

            }
            
            foreach ($current_events_params as $current_event_params) {
                $eventContainerList->pushEventContainer($this->eventFactory->makeEventContainer($current_event_params));
            }
            $offset+= static::READ_OFFSET;

        }

        $connection->set(static::PROCESS_CHANGE_KEY, static::PROCESS_CHANGE_OFF);
        return $eventContainerList;
        
    }

    protected function generateEventUid(): string
    {
        $uid_str = $this->generateEventUidString();

        while (null !== $this->redisClient->getConnection()->hKeys(static::EVENT_KEY . $uid_str)) {
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

    protected function checkEventsIsInChangeProcess(): bool
    {
        $in_process = $this->redisClient->getConnection()->get(static::PROCESS_CHANGE_KEY);

        return "1" === $in_process ? true : false;
    }




}