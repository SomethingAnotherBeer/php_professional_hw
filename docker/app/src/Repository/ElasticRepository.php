<?php
declare(strict_types=1);
namespace App\Repository;

use App\Client\ElasticClient;
use App\Entity\Event\Event;
use App\Mapper\Event\ElasticEventMapper;

class ElasticRepository
{
    protected ElasticClient $elasticClient;
    protected ElasticEventMapper $eventMapper;

    public function __construct(ElasticClient $elasticClient, ElasticEventMapper $eventMapper)
    {
        $this->elasticClient = $elasticClient;
        $this->eventMapper = $eventMapper;
    }

    public function getEventByUid(string $event_uid)
    {
        
    }

    public function createEvent(Event $event)
    {

    }

    public function changeEvent(Event $event)
    {

    }

    public function removeEvent(Event $event)
    {

    }




}