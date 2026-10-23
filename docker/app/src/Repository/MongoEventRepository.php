<?php
declare(strict_types=1);
namespace App\Repository;

use App\Client\MongoClient;
use App\Entity\Event\Event;
use App\Mapper\Event\MongoEventMapper;
use App\Model\Event\Event as EventModel;
use App\Model\Event\EventCondition\EventConditionList;

class MongoEventRepository
{
    protected MongoClient $mongoClient;
    protected MongoEventMapper $eventMapper;

    public const COLLECTION_NAME = "events_collection";

    public function __construct(MongoClient $mongoClient, MongoEventMapper $eventMapper)
    {
        $this->mongoClient = $mongoClient;
        $this->eventMapper = $eventMapper;
    
        $this->mongoClient->setCollection(static::COLLECTION_NAME);

    }


    public function getEventByUid(string $event_uid): ?Event
    {   
        $event = null;

        $item = $this->mongoClient->getCollection()->findOne(['event_uid' => $event_uid]);
        if ($item) {
            $event = $this->eventMapper->fromSourceToEntity($item);
        }
        return $event;
    }

    public function createEvent(Event $event): Event
    {
        $event_params = $this->eventMapper->fromEntityToSource($event);
        $this->mongoClient->getCollection()->insertOne($event_params, ['_id' => $event->getEventUid()]);

        return $event;
    }

    public function changeEvent(string $event_uid, EventModel $event): Event
    {
        $event_params = [];

        if (null !== $event->getEventPriority()) {
            $event_params['event_priority'] = $event->getEventPriority();
        }

        if (null !== $event->getEventConditionList()) {
            $event_params['event_condition_list'] = $event->getEventConditionList();
        }

        if (null !== $event->getEventValueList()) {
            $event_params['event_value_list'] = $event->getEventValueList();
        }

        $this->mongoClient->getCollection()->updateOne(['_id' => $event_uid], ['$set' => $event_params]);
        return $this->getEventByUid($event_uid);

    }

    public function removeEvent(Event $event): Event
    {
        $this->mongoClient->getCollection()->deleteOne(['_id' => $event->getEventUid()]);
        return $event;
    }

    /**
     * @param EventConditionList $eventConditionList
     * @return Event[]
     */
    public function findEventsByConditionList(EventConditionList $eventConditionList): array
    {   
        /** @var Event[] $event_list */
        $event_list = [];
        $item_list = [];

        $limit = 100;
        $skip = 0;
        $is_not_empty = true;

        $collection = $this->mongoClient->getCollection();

        while ($is_not_empty) {
            $current_count = 0;
            $items = $collection->find(['$eq' => ['event_condition_list' => $eventConditionList->asArray()]], ['limit' => $limit, 'skip' => $skip]);

            foreach ($items as $item) {
                $current_count+=1;
                $item_list[] = $item;
            }

            if (0 === $current_count) {
                $is_not_empty = false;
            }

        }

        $event_list = array_map(fn(array $event_params) => $this->eventMapper->fromSourceToEntity($event_params), $item_list);
        return $event_list;


    }

    public function clearEvents(): int
    {
        $response = $this->mongoClient->getCollection()->deleteMany([]);
        return $response->getDeletedCount();
    }


}