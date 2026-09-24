<?php
declare(strict_types=1);
namespace App\Repository\Event;

use App\Client\ElasticClient;
use App\Entity\Event;
use App\Exception\Event\EventNotFoundException;
use App\Mapper\Event\ElasticEventMapper;
use App\ValueObject\Event\EventConditionList;

class ElasticEventRepository
{
    private ElasticClient $elasticClient;
    private ElasticEventMapper $eventMapper;

    public function __construct(ElasticClient $elasticClient, ElasticEventMapper $eventMapper)
    {
        $this->elasticClient = $elasticClient;
        $this->eventMapper = $eventMapper;
    }
    

    /**
     * @param EventConditionList $eventConditionList
     * @return Event[]
     */
    public function getEventsByConditionList(EventConditionList $eventConditionList): array
    {
        $query = 
        [
            'query' =>
            [
                'bool' =>
                [
                    'must' =>
                    [

                    ],
                ]
            ]  
        ];

        $prepared_event_condition_list = [];

        foreach ($eventConditionList->all() as $eventCondition) {
            $prepared_event_condition_list[] = $this->eventMapper->prepareEventConditionValueToSource($eventCondition);
        }
        foreach ($prepared_event_condition_list as $prepared_event_condition) {
            $nested =
            [
                'path' => 'conditions',
                'query' =>
                [
                    'bool' =>
                    [
                        'must' =>
                        [
                            [
                                'term' => ['conditions.key' => $prepared_event_condition['key']]
                            ],
                            [
                                'term' => ['conditions.value' => $prepared_event_condition['value']]
                            ],
                        ]
                    ]
                ]
            ];
            $query['query']['bool']['must'][] = ['nested' => $nested];
        }

        $response = $this->elasticClient->search($query);
        $response = $response->asArray();
        $response = $response['hits']['hits'] ?? [];

        /** @var Event[] $event_list */
        $event_list = [];
        foreach ($response as $item) {
            $event_list[] = $this->eventMapper->makeEntityFromSource($item);
        }

        return $event_list;


    }

    public function getEvent(string $uid): Event
    {
        $event = $this->getEventOrNull($uid);
        if (!$event) {
            throw new EventNotFoundException("Событие не найдено");
        }
        return $event;

    }

    public function flushEvent(Event $event): Event
    {
        $this->eventMapper->makeSourceFromEntity($event);
        return $event;
    }

    public function removeEvent(Event $event): Event
    {
        try {
            $this->elasticClient->delete($event->getEventUid());
        }
        catch(\Exception $e) {
            
        }

        return $event;
    }

    public function clearEvents()
    {
        $query = ['query' => []];
        $response = $this->elasticClient->deleteByQuery($query);
    }

    private function getEventOrNull(string $uid): ?Event
    {
        $event_response = null;

        try {
            $event_response = $this->elasticClient->get($uid);
            $event_response = $event_response->asArray();
        }
        catch(\Exception $e) {
            $event_response = null;
        }
        
        return null !== $event_response ? $this->eventMapper->makeEntityFromSource($event_response) : null;

    }

}