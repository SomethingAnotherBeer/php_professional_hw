<?php
declare(strict_types=1);
namespace App\DTO;

class EventDTO
{
    private ?string $uid = null;

    private ?int $priority = null;

    private ?EventConditionListDTO $eventConditionListDTO = null;

    private ?EventValueListDTO $eventValueListDTO = null;


    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = trim(htmlspecialchars($uid));

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getEventConditionList(): EventConditionListDTO
    {
        return $this->eventConditionListDTO;
    }

    public function setEventConditionList(EventConditionListDTO $eventConditionListDTO): static
    {
        $this->eventConditionListDTO = $eventConditionListDTO;

        return $this;
    }

    public function getEventValueList(): EventValueListDTO
    {
        return $this->eventValueListDTO;
    }

    public function setEventValueList(EventValueListDTO $eventValueListDTO): static
    {
        $this->eventValueListDTO = $eventValueListDTO;

        return $this;
    }

    public static function fromArray(array $params): EventDTO
    {
        $eventDTO = new EventDTO();

        if (isset($params['uid']) && is_string($params['uid'])) {
            $eventDTO->setUid($params['uid']);
        }

        if (isset($params['priority']) && is_int($params['priority'])) {
            $eventDTO->setPriority($params['priority']);
        }

        if (isset($params['event_condition_list']) && is_array($params['event_condition_list'])) {
            $eventDTO->setEventConditionList(EventConditionListDTO::makeInstance($params['event_condition_list']));
        }

        if (isset($params['event_value_list']) && is_array($params['event_value_list'])) {
            $eventDTO->setEventValueList(EventValueListDTO::makeInstance($params['event_value_list']));
        }

        return $eventDTO;

    }




}