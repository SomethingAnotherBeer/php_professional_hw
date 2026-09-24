<?php
declare(strict_types=1);
namespace App\Repository\Event;


use App\Client\RedisClient;
use App\Exception\Event\EventInChangeProcessException;
use App\Exception\Event\EventNotFoundException;
use App\Entity\Event;
use App\Factory\EventFactory;
use App\Mapper\Event\RedisEventMapper;
use App\ValueObject\Event\EventConditionList;

class RedisEventRepository implements EventRepositoryInterface
{
    public const EVENT_KEY = "event";
    public const PROCESS_CHANGE_KEY = "process_change";
    public const PROCESS_CHANGE_ON = "1";
    public const PROCESS_CHANGE_OFF = "0";
    public const READ_OFFSET = 100;

    private RedisClient $redisClient;
    private RedisEventMapper $eventMapper;

    public function __construct(RedisClient $redisClient, RedisEventMapper $eventMapper)
    {
        $this->redisClient = $redisClient;
        $this->eventMapper = $eventMapper;
    }

    public function getEventsByConditionList(EventConditionList $eventConditionList): array
    {
        $connection = $this->redisClient->getConnection();

        if ($this->checkEventsIsInChangeProcess()) {
            throw new EventInChangeProcessException("На данный момент данные событий изменяются, попробуйте позже");
        }

        /** @var Event[] $coinciding_events */
        $coinciding_events = [];
        $offset = static::READ_OFFSET;

        while (($current_event_keys = $connection->scan($offset, static::EVENT_KEY, 100)) !== null) {
            foreach ($current_event_keys as $current_event_key) {
                $event = $this->getEventOrNull($current_event_key);
                if ($event->getEventConditionList()->asArray() === $eventConditionList->asArray()) {
                    $coinciding_events[] = $event;
                }
                
            }

            $offset+= static::READ_OFFSET;

        }

        if (count($coinciding_events) > 0) {
            $event_max_priority = 0;
            foreach ($coinciding_events as $coincidingEvent) {
                if ($coincidingEvent->getEventPriority() > $event_max_priority) {
                    $event_max_priority = $coincidingEvent->getEventPriority();
                }
            }

            $coinciding_events = array_filter($coinciding_events, fn(Event $eventContainer) => $eventContainer->getEventPriority() === $event_max_priority);

        }

        return $coinciding_events;

    }   

    public function getEvent(string $uid): Event
    {
        $event = $this->getEventOrNull($uid);
        if (!$event) {
            throw new EventNotFoundException("Событие не найдено");
        }

        return $event;
    }

    public function flushEvent(Event $event): Event
    {

        $event_params = $this->eventMapper->makeSourceFromEntity($event);
        $event_params = json_encode($event_params, JSON_UNESCAPED_UNICODE);

        $this->redisClient->getConnection()->set(static::EVENT_KEY . $event->getEventUid(), $event_params);

        return $event;

    }

    public function removeEvent(Event $event): Event
    {   
        $this->redisClient->getConnection()->del(static::EVENT_KEY . $event->getEventUid());
        return $event;
    }


    public function clearEvents(): array
    {
        $connection = $this->redisClient->getConnection();

        if ($this->checkEventsIsInChangeProcess()) {
            throw new EventInChangeProcessException("На данный момент данные событий изменяются, попробуйте позже");
        }
        $connection->set(static::PROCESS_CHANGE_KEY, static::PROCESS_CHANGE_ON);

        $offset = static::READ_OFFSET;

        /** @var Event[] $current_event_params_list */
        $current_event_params_list = [];

        while (($current_event_keys = $connection->scan($offset, static::EVENT_KEY)) !== null) {
            foreach ($current_event_keys as $current_event_key) {
                $current_event_params_list[] = $this->getEventOrNull($current_event_key);            
                $connection->del(static::EVENT_KEY . $current_event_key);

            }

            $offset+= static::READ_OFFSET;

        }

        $connection->set(static::PROCESS_CHANGE_KEY, static::PROCESS_CHANGE_OFF);
        return $current_event_params_list;
        
    }

    public function getEventOrNull(string $uid): ?Event
    {
        $event_params = $this->redisClient->getConnection()->get(static::EVENT_KEY . $uid);
        $event = $event_params ? $this->eventMapper->makeEntityFromSource($event_params) : null;

        return $event;
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