<?php
declare(strict_types=1);
namespace App\Controller;

use App\DTO\Event\EventConditionListDTO;
use App\DTO\Event\EventDTO;
use App\Service\EventService;

class EventController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function getEvent(string $event_uid): array
    {
        $eventDTO = EventDTO::fromArray(['event_uid' => $event_uid]);
        $response = $this->eventService->getEvent($eventDTO);

        return $response->asArray();
    }

    public function createEvent(array $params): array
    {
        $eventDTO = EventDTO::fromArray($params);
        $response = $this->eventService->createEvent($eventDTO);

        return $response->asArray();
    }

    public function changeEvent(string $event_uid, array $params): array
    {
        $params['event_uid'] = $event_uid;
        $eventDTO = EventDTO::fromArray($params);

        $response = $this->eventService->changeEvent($eventDTO);

        return $response->asArray();
    }

    public function removeEvent(string $event_uid): array
    {
        $eventDTO = EventDTO::fromArray(['event_uid' => $event_uid]);
        $response = $this->eventService->removeEvent($eventDTO);

        return $response->asArray();
    }

    public function getEventsByConditionList(array $params): array
    {
        $eventConditionListDTO = EventConditionListDTO::makeInstance($params);
        $response = $this->eventService->getEventsByConditionList($eventConditionListDTO);

        return $response->asArray();    
    }

    public function clearEvents(): array
    {
        $response = $this->eventService->clearEvents();
        return $response->asArray();
    }
}