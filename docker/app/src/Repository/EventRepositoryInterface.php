<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Event\Event;
use App\Model\Event\EventCondition\EventConditionList;

interface EventRepositoryInterface
{
    public function getEventByUid(string $event_uid): ?Event;

    public function createEvent(Event $event): Event;

    public function changeEvent(Event $event): Event;

    public function removeEvent(Event $event): Event;

    /**
     * @param EventConditionList $eventConditionList
     * @return Event[]
     */
    public function findEventsByConditionList(EventConditionList $eventConditionList): array;

    public function clearEvents(): int;

}