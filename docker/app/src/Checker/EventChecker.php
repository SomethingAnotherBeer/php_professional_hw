<?php
declare(strict_types=1);
namespace App\Checker;

use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventNotSpecifiedException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\ValueObject\Event\EventContainer;

class EventChecker
{

    public function checkEventForCreate(EventContainer $eventContainer): void
    {
        if (null === $eventContainer->getPriority()) {
            throw new EventPriorityNotSpecifiedException("Не указан приоритет события");
        }

        if (null === $eventContainer->getEventConditionList() || 0 === $eventContainer->getEventConditionList()->count()) {
            throw new EventConditionListNotSpecifiedException("Не указан список условий параметров возникновения события");
        }
        
        if (null === $eventContainer->getEvent() || 0 === $eventContainer->getEvent()->count()) {
            throw new EventNotSpecifiedException("Событие не указано");
        }



    }

    public function checkEventForUpdate(EventContainer $eventContainer): void
    {
        if (null === $eventContainer->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
    }

    public function checkEventForDelete(EventContainer $eventContainer): void
    {
        if (null === $eventContainer->getUid()) {
            throw new EventUidNotSpecifiedException("Не указан идентификатор события");
        }
    }


}