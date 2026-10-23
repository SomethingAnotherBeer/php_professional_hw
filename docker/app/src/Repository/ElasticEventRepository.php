<?php
declare(strict_types=1);
namespace App\Repository;

use App\Client\ElasticClient;
use App\Client\RedisClient;
use App\Entity\Event\Event;
use App\Mapper\Event\ElasticEventMapper;
use Elastic\Elasticsearch\Response\Elasticsearch;
use App\Model\Event\EventCondition\EventConditionList;

class ElasticEventRepository implements EventRepositoryInterface
{
    protected ElasticClient $elasticClient;
    protected RedisClient $redisClient;
    protected ElasticEventMapper $eventMapper;



    public function __construct(ElasticClient $elasticClient, RedisClient $redisClient, ElasticEventMapper $eventMapper)
    {
        $this->elasticClient = $elasticClient;
        $this->redisClient = $redisClient;
        $this->eventMapper = $eventMapper;
    }

    public function getEventByUid(string $event_uid): ?Event
    {
        $params = 
        [
            'index' => $this->elasticClient->getIndex(),
            'id' => $event_uid,
        ];
        $response = $this->elasticClient->getConnection()->get($params);
        $response_arr = $this->getPrepared($response);

        $event = null;

        if ($response_arr) {
            $event = $this->eventMapper->fromSourceToEntity($response_arr);
        }

        return $event;

        
    }

    public function createEvent(Event $event): Event
    {
        $params =
        [
            'index' => $this->elasticClient->getIndex(),
            'id' => $event->getEventUid(),
            'body' => $this->eventMapper->fromEntityToSource($event),
        ];

        $this->elasticClient->getConnection()->index($params);
        return $event;


    }

    public function changeEvent(Event $event): Event
    {
        $params =
        [
            'index' => $this->elasticClient->getIndex(),
            'id' => $event->getEventUid(),
            'body' => $this->eventMapper->fromEntityToSource($event),
        ];

        $this->elasticClient->getConnection()->index($params);
        return $event;
    }

    public function removeEvent(Event $event): Event
    {
        $params =
        [
            'index' => $this->elasticClient->getIndex(),
            'id' => $event->getEventUid(),
        ];
        $this->elasticClient->getConnection()->delete($params);

        return $event;
    }

    /** @param EventConditionList $eventConditionList
     *  @return Event[] 
     */
    public function findEventsByConditionList(EventConditionList $eventConditionList): array
    {
        $prepared_event_condition_list = $this->eventMapper->mapEventConditionListToSource($eventConditionList);

        $nested_params = [];

        foreach ($prepared_event_condition_list as $event_condition_params) {
            $nested_params[] = 
            [
                'bool' =>
                [   
                    'must' =>
                    [
                        [
                            'term' => ['event_condition_list.key' => $event_condition_params['key']],
                        ],

                        [
                            'term' => ['event_condition_list.value' => $event_condition_params['value']],
                        ],

                        [
                            'term' => ['event_condition_list.type' => $event_condition_params['type']],
                        ],

                    ],
                ],
            ];
        }

        $query_params =
        [
            'query' =>
            [
                'bool' =>
                [
                    'must' =>
                    [
                        'term' => ['event_condition_list_count' => count($prepared_event_condition_list)],
                    ],

                    'must_not' =>
                    [
                        'nested' =>
                        [
                            'path' => 'event_condition_list',
                            'query' =>
                            [
                                'bool' =>
                                [
                                    'must_not' =>
                                    [
                                        'bool' =>
                                        [
                                            'should' => $nested_params,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $query = 
        [
            'index' => $this->elasticClient->getIndex(),
            'body' => $query_params,
        ];
        $response = $this->elasticClient->getConnection()->search($query);
        $response = $response->asArray();

        /** @var Event[] $event_list */
        $event_list = [];

        if ($response['hits']['total']['value'] > 0) {
            foreach ($response['hits']['hits'] as $params) {
                $event_list[] = $this->eventMapper->fromSourceToEntity($params['_source']);
            }
        }

        return $event_list;



    }

    public function clearEvents(): int
    {   
        $connection = $this->elasticClient->getConnection();
        $count = $connection->count()->asArray();
        $count = $count['count'];

        $connection->indices()->delete(['index' => $this->elasticClient->getIndex()]);
        $connection->indices()->create(['index' => $this->elasticClient->getIndex(), 'body' => ['mappings' => $this->getMappings()]]);

        return $count;
    }



    protected function getPrepared(Elasticsearch $response): array
    {
        $response_arr = $response->asArray();
        $response_arr = true === $response_arr['found'] ? $response_arr['_source'] : [];

        return $response_arr;
    }

    protected function getMappings()
    {
        return 
        [
            'properties' =>
            [
                'event_uid' => 
                [
                    "type" => "keyword",
                ],
                'event_priority' => 
                [
                    "type" => "integer",
                ],
                'event_condition_list_count' =>
                [
                    "type" => "integer"
                ],
                'event_condition_list' =>
                [
                    "type" => "nested",
                    "properties" =>
                    [
                        "key" =>
                        [
                            "type" => "keyword",
                        ],
                        "value" =>
                        [
                            "type" => "keyword",
                        ],  
                        "type" =>
                        [   
                            "type" => "keyword",
                        ],      
                    ],
                ],
                "event_value_list_count" =>
                [
                    "type" => "integer",
                ],
                "event_value_list" =>
                [
                    "type" => "nested",
                    "properties" =>
                    [
                        "key" =>
                        [
                            "type" => "keyword",
                        ],
                        "value" =>
                        [
                            "type" => "keyword",
                        ],
                        "type" =>
                        [
                            "type" => "keyword",
                        ],
                    ],  
                ],
            ],
        ];
    }

}