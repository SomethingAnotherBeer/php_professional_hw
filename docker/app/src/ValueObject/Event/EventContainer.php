<?php
declare(strict_types=1);
namespace App\ValueObject\Event;

class EventContainer
{
    private ?string $uid;

    private int $priority;

    private EventConditionList $eventConditionList;

    private Event $event;


    public function __construct(array $params = [])
    {
        $this->uid = $params['uid'] ?? null;
        $this->priority = $params['priority'];
        $this->eventConditionList = $params['event_condiition_list'];
        $this->event = $params['event'];
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;
        
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getEventConditionList(): EventConditionList
    {
        return $this->eventConditionList;
    }

    public function setEventConditionList(EventConditionList $eventConditionList): static
    {
        $this->eventConditionList = $eventConditionList;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function asArray(array $include = []): array
    {
        $params = 
        [
            'uid' => fn(): string => $this->uid,
            'priority' => fn(): int => $this->priority,
            'event_condition_list' => fn(): array => $this->eventConditionList->asArray(),
            'event' => fn(): array => $this->event->asArray(),
        ];
        $target_params = [];

        foreach ($params as $param_key => $callback) {
            if (isset($include[$param_key]) && true === $include[$param_key]) {
                $target_params[$param_key] = $callback();
            }
        }

        return $target_params;

    }


}