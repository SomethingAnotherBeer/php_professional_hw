<?php
declare(strict_types=1);
namespace App\Repository\Event;

use App\Client\ElasticClient;
use App\Entity\Event;
use App\ValueObject\Event\EventConditionList;

class ElasticEventRepository
{
    private ElasticClient $elasticClient;


    public function getEventsByConditionList(EventConditionList $eventConditionList)
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




        foreach ($eventConditionList as $event_condition_key => $event_condition_value) {
            $nested =
            [
                'path' => 'conditions',
                'query' =>
                [
                    'bool' =>
                    [
                        'must' =>
                        [
                            'term' => ['conditions.key' => $event_condition_key],
                            
                        ],
                        [
                            'term' => ['conditions.value' => $event_condition_value],
                        ],
                    ]
                ]
            ];

            $query['query']['bool']['must'][] = ['nested' => $nested];
            
            $response = $this->elasticClient->search($query);
            $response = $response->asArray();
            $response = $response['hits']['hits'] ?? [];

            $prepared_response = [];

            foreach ($response as $item) {
                $current =
                [
                    'uid' => $item['uid'],
                    'priority' => $item['priority'],
                    'event_condition_list' => $item['event_condition_list'],
                    'event_value' => $item['event_value'],
                ];  

                foreach ($item['event_condition_list'] as $event_condition) {
                    $current['event_condition_list'][][$event_condition['key']] = $event_condition['value'];
                }

                foreach ($item['event_value'] as $event_value) {
                    $current['event_condition_list'][$event_value['key']] = $event_value['value'];
                }

                $prepared_response[] = $current;

            }



        }   


    }

    public function getEvent(string $uid)
    {
        
    }

    public function flushEvent(Event $event)
    {

    }

    public function removeEvent(Event $event)
    {

    }

    public function clearEvents()
    {
        
    }
}