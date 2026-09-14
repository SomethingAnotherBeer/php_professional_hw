<?php
declare(strict_types=1);
namespace App\Repository\Event;

use App\ValueObject\Event\EventConditionList;
use App\ValueObject\Event\EventContainer;
use App\ValueObject\Event\EventContainerList;
use App\ValueObject\Query\EventQuery;

interface EventRepositoryInterface
{
    public function getEventsByConditionList(EventConditionList $eventConditionList): EventContainerList;

    public function getEventByUid(string $uid): EventContainer;

    public function clearEvents(): EventContainerList;

    public function createEvent(EventContainer $eventContainer): EventContainer;

    public function changeEvent(EventContainer $eventContainer): EventContainer;

    
}