<?php
declare(strict_types=1);
namespace App\Repository\Event;

use App\Entity\Event;
use App\ValueObject\Event\EventConditionList;

interface EventRepositoryInterface
{   
    /**
     * @param EventConditionList $eventConditionList
     * @return Event[]
     */
    public function getEventsByConditionList(EventConditionList $eventConditionList): array;

    public function getEvent(string $uid): Event;
    
    public function getEventOrNull(string $uid): ?Event;

    /**
     * @return Event[]
     */
    public function clearEvents(): array;

    public function flushEvent(Event $event): Event;

    public function removeEvent(Event $event): Event;
    

    
}