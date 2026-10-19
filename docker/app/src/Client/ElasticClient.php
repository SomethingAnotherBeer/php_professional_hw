<?php
declare(strict_types=1);
namespace App\Client;

use App\Model\Client\ElasticClientValue;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticClient
{
    protected static ?ElasticClient $elasticClient = null;

    protected Client $client;

    protected string $index;

    public static function makeInstance(ElasticClientValue $elasticClientValue)
    {
        if (!static::$elasticClient) {
            static::$elasticClient = new ElasticClient($elasticClientValue);
        }
    }

    protected function __construct(ElasticClientValue $elasticClientValue)
    {
        $this->client = ClientBuilder::create()->setHosts([$elasticClientValue->getElasticHost()])
        ->setBasicAuthentication($elasticClientValue->getElasticUser(), $elasticClientValue->getElasticPassword())->build();
        
        $this->index = $elasticClientValue->getElasticIndex();
    }

    public function getConnection(): Client
    {
        return $this->client;
    }

    public function getIndex(): string
    {
        return $this->index;
    }


}