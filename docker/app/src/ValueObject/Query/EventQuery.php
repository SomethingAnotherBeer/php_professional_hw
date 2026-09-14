<?php
declare(strict_types=1);
namespace App\ValueObject\Query;

use App\ValueObject\Event\EventConditionList;

class EventQuery
{
    private ?string $uid = null;

    private ?int $priority_eq = null;

    private ?int $priority_from = null;

    private ?int $priority_to = null;

    private ?EventConditionList $eventConditionList = null;

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getPriorityEq(): ?int
    {
        return $this->priority_eq;
    }

    public function setPriorityEq(int $priority_eq): static
    {
        $this->priority_eq = $priority_eq;

        return $this;
    }

    public function getPriorityFrom(): ?int
    {
        return $this->priority_from;
    }

    public function setPriorityFrom(int $priority_from): static
    {
        $this->priority_from = $priority_from;
        
        return $this;
    }

    public function getPriorityTo(): ?int
    {
        return $this->priority_to;
    }

    public function setPriorityTo(int $priority_to): static
    {
        $this->priority_to = $priority_to;

        return $this;
    }


    public function getEventConditionList(): ?EventConditionList
    {
        return $this->eventConditionList;
    }
    
    public function setEventConditionList(EventConditionList $eventConditionList): static
    {
        $this->eventConditionList = $eventConditionList;

        return $this;
    }

}