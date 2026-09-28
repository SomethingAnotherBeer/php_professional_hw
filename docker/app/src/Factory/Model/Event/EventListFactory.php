<?php
declare(strict_types=1);
namespace App\Factory\Model\Event;
use App\Entity\Event\Event as EventEntity;
use App\Model\Event\EventList;

class EventListFactory
{
    private EventFactory $eventFactory;

    public function __construct(EventFactory $eventFactory)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param EventEntity[] $entity_list
     */
    public function makeEventListFromEntityList(array $entity_list)
    {
        $eventList = EventList::makeInstance();
        foreach ($entity_list as $event) {
            $eventList->push($this->eventFactory->makeEventFromEntity($event));
        }

        return $eventList;
    }
}