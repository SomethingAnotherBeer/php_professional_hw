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


    public function get(string $id): Elasticsearch
    {
        $params =
        [
            'index' => $this->index,
            'id' => $id,
        ];

        return $this->elasticConnection->get($params);
    }

    public function index(string $id, array $params): Elasticsearch
    {
        $query =
        [
            'index' => $this->index,
            'id' => $id,
            'body' => $params,
        ];
        $response = $this->elasticConnection->index($query);
        return $response;
    }

    public function delete(string $id): Elasticsearch
    {
        $query =
        [
            'index' => $this->index,
            'id' => $id,
        ];
        $response = $this->elasticConnection->delete($query);

        return $response;
    }

    public function deleteByQuery(array $query): Elasticsearch
    {
        $params =
        [
            'index' => $this->index,
            'body' => $query,
        ];

        $response = $this->elasticConnection->deleteByQuery($params);

        return $response;
    }   


}