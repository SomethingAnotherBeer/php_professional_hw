<?php
declare(strict_types=1);
namespace App\Controller;

use App\Builder\Http\ResponseBuilder;
use App\DTO\Event\EventConditionListDTO;
use App\DTO\Event\EventDTO;
use App\Http\Response;
use App\Service\EventService;

class EventController
{
    private EventService $eventService;
    private ResponseBuilder $responseBuilder;

    public function __construct(EventService $eventService, ResponseBuilder $responseBuilder)
    {
        $this->eventService = $eventService;
        $this->responseBuilder = $responseBuilder;
    }

    public function getEvent(string $event_uid): Response
    {
        $eventDTO = EventDTO::fromArray(['event_uid' => $event_uid]);
        $response = $this->eventService->getEvent($eventDTO);

        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(200)->getResponse(); 
    }

    public function createEvent(array $params): Response
    {
        $eventDTO = EventDTO::fromArray($params);
        $response = $this->eventService->createEvent($eventDTO);

        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(201)->getResponse();

    }

    public function changeEvent(string $event_uid, array $params): Response
    {
        $params['event_uid'] = $event_uid;
        $eventDTO = EventDTO::fromArray($params);

        $response = $this->eventService->changeEvent($eventDTO);

        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(200)->getResponse();
    }

    public function removeEvent(string $event_uid): Response
    {
        $eventDTO = EventDTO::fromArray(['event_uid' => $event_uid]);
        $response = $this->eventService->removeEvent($eventDTO);

        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(200)->getResponse();
    }

    public function getEventsByConditionList(array $params): Response
    {
        $eventConditionListDTO = EventConditionListDTO::makeInstance($params);
        $response = $this->eventService->getEventsByConditionList($eventConditionListDTO);

        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(200)->getResponse();  
    }

    public function clearEvents(): Response
    {
        $response = $this->eventService->clearEvents();
        return $this->responseBuilder->asJson($response->asArray())->setStatusCode(200)->getResponse(); 
    }
}