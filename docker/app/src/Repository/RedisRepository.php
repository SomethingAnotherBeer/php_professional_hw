<?php
declare(strict_types=1);
namespace App\Repository;
use App\Model\Event\EventCondition\EventConditionList;
use App\Entity\Event\Event;
use App\Client\RedisClient;
use App\Mapper\Event\RedisEventMapper;
use App\Model\Event\Event as EventModel;
use App\Trait\Event\EventCompareTrait;

class RedisRepository implements EventRepositoryInterface
{
    use EventCompareTrait;

    private RedisClient $redisClient;
    private RedisEventMapper $eventMapper;

    public const EVENT_PREFIX = "event:";

    public const LOCKING_KEY = "is_locking";
    public const IS_ENABLE_LOCKING = "1";
    public const IS_DISABLE_LOCKING = "0";


    public function __construct(RedisClient $redisClient, RedisEventMapper $eventMapper)
    {
        $this->redisClient = $redisClient;
        $this->eventMapper = $eventMapper;
    }

    public function getEventByUid(string $event_uid): ?Event
    {   
        $event = null;

        $event_params = $this->redisClient->getConnection()->get($this->prepareEventUid($event_uid));

        if ($event_params) {
            $event_params = json_decode($event_params, true);
            $event = $this->eventMapper->fromSourceToEntity($event_params);
        }

        return $event;


    }

    public function createEvent(Event $event): Event
    {
        $event_params = $this->eventMapper->fromEntityToSource($event);
        $event_params = json_encode($event_params, JSON_UNESCAPED_UNICODE);

        $this->redisClient->getConnection()->set($this->prepareEventUid($event->getEventUid()), $event_params);

        return $event;
    }

    public function changeEvent(string $event_uid, EventModel $event): Event
    {

        $target = $this->getEventByUid($event_uid);
        $target_params = $this->eventMapper->fromEntityToSource($target);
        $changed_params = $event->asArray();

        $changed_rows = array_intersect(array_keys($changed_params), array_keys($target_params));

        foreach ($changed_rows as $changed_row) {
            $target_params[$changed_row] = $changed_params[$changed_row];
        }

        $event_params = json_encode($target_params, JSON_UNESCAPED_UNICODE);

        $this->redisClient->getConnection()->set($this->prepareEventUid($event_uid), $event_params);

        return $this->eventMapper->fromSourceToEntity($target_params);
    }

    public function removeEvent(Event $event): Event
    {
        $this->redisClient->getConnection()->del($this->prepareEventUid($event->getEventUid()));
        return $event;
    }

    /**
     * @param EventConditionList $eventConditionList
     * @return Event[]
     */
    public function findEventsByConditionList(EventConditionList $eventConditionList): array
    {
        $coinciding_event_list = [];
        $iterator = null;
        $count = 100;
        $connection = $this->redisClient->getConnection();

        $keys = [];
        
        while ($keys = $connection->scan($iterator, static::EVENT_PREFIX . "*", $count)) {
            $event_params_list = $connection->mget($keys);
            if (is_array($event_params_list) && count($event_params_list) > 0) {
                foreach ($event_params_list as $event_param) {
                    $event = $this->eventMapper->fromSourceToEntity(json_decode($event_param, true));
                    if ($this->compareEventConditionListsIsEquals($event->getEventConditionList(), $eventConditionList)) {
                        $coinciding_event_list[] = $event;
                    }
                }
            } 
        }

        return $coinciding_event_list;   
        
    }

    public function clearEvents(): int
    {
        $count = 100;
        $removed_count = 0;
        $current_removed_count = 0;
        $connection = $this->redisClient->getConnection();

        $keys = [];

        while ($keys = $connection->scan($iterator, static::EVENT_PREFIX . "*", $count)) {
            $current_removed_count = $connection->del(...$keys);
            if ($current_removed_count) {
                $removed_count+= $current_removed_count;
            }
        }

        return $removed_count;
    }

    protected function enableLocking(): void
    {
        $this->redisClient->getConnection()->set(static::LOCKING_KEY, static::IS_ENABLE_LOCKING);
    }

    protected function disableLocking(): void
    {
        $this->redisClient->getConnection()->set(static::LOCKING_KEY, static::IS_DISABLE_LOCKING);
    }

    protected function isLocking(): bool
    {   
        $is_locking = false;
        $current_locking = $this->redisClient->getConnection()->get(static::LOCKING_KEY);

        if (null === $current_locking || static::IS_DISABLE_LOCKING === $current_locking) {
            $is_locking = false;
        }
        else if (static::IS_ENABLE_LOCKING === $current_locking) {
            $is_locking = true;
        }

        return $is_locking;
        
    }

    protected function prepareEventUid(string $event_uid): string
    {
        return static::EVENT_PREFIX . $event_uid;
    }

}