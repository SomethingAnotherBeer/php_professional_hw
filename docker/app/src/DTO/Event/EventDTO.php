<?php
declare(strict_types=1);
namespace App\DTO\Event;

class EventDTO
{
    private ?string $event_uid;

    private ?int $event_priority;

    private ?EventConditionListDTO $eventConditionListDTO;

    private ?EventValueListDTO $eventValueListDTO;

    public function __construct
    (
        ?string $event_uid = null,
        ?int $event_priority = null,
        ?EventConditionListDTO $eventConditionListDTO = null,
        ?EventValueListDTO $eventValueListDTO = null
    )
    {
        $this->event_uid = $event_uid;
        $this->event_priority = $event_priority;
        $this->eventConditionListDTO = $eventConditionListDTO;
        $this->eventValueListDTO = $eventValueListDTO;
    }

    public static function fromArray(array $params): EventDTO
    {
        $event_uid = null;
        $event_priority = null;
        $eventConditionList = null;
        $eventValueList = null;

        if (isset($params['event_uid']) && is_int($params['event_uid'])) {
            $event_uid = $params['event_uid'];
        }

        if (isset($params['event_priority']) && is_int($params['event_priority'])) {
            $event_priority = $params['event_priority'];
        }

        if (isset($params['event_condition_list']) && is_array($params['event_condition_list'])) {
            $eventConditionList = EventConditionListDTO::makeInstance($params['event_condition_list']);
        }

        if (isset($params['event_value_list']) && is_array($params['event_value_list'])) {
            $eventValueList = EventValueListDTO::makeInstance($params['event_value_list']);
        }

        return new EventDTO($event_uid, $event_priority, $eventConditionList, $eventValueList);


    }

    public function getEventUid(): ?string
    {
        return $this->event_uid;
    }

    public function getEventPriority(): ?int
    {
        return $this->event_priority;
    }

    public function getEventConditionListDTO(): ?EventConditionListDTO
    {
        return $this->eventConditionListDTO;
    }

    public function getEventValueListDTO(): ?EventValueListDTO
    {
        return $this->eventValueListDTO;
    }


}