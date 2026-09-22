<?php
declare(strict_types=1);
namespace App\Client;

use App\ValueObject\Connection\ElasticConnectionValue;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;

class ElasticClient
{
    protected static ?ElasticClient $elasticClient = null;

    protected Client $elasticConnection;

    protected string $index;

    public static function makeInstance(ElasticConnectionValue $elasticConnectionValue): ElasticClient
    {
        if (!static::$elasticClient) {
            static::$elasticClient = new ElasticClient($elasticConnectionValue);
        }

        return static::$elasticClient;
    }

    protected function __construct(ElasticConnectionValue $elasticConnectionValue)
    {
        $connection = ClientBuilder::create()->setHosts([$elasticConnectionValue->getElasticUrl()])
            ->setBasicAuthentication($elasticConnectionValue->getElasticUser(), $elasticConnectionValue->getElasticPassword())
            ->build();

        $this->elasticConnection = $connection;
        $this->index = $elasticConnectionValue->getElasticIndex();
    }


    public function search(array $params): Elasticsearch
    {
        $query =
        [
            'index' => $this->index,
            'body' => $params,
        ];

        $response = $this->elasticConnection->search($query);

        return $response;
    }




}