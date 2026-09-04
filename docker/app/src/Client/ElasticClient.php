<?php
declare(strict_types=1);
namespace App\Client;

use App\Exception\Client\ClientHostNotSpecifiedException;
use App\Exception\Client\ClientPasswordNotSpecifiedException;
use App\Exception\Client\ClientTargetNotSpecifiedException;
use App\Exception\Client\ClientUserNotSpecifiedException;
use App\Exception\Query\InvalidQueryException;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;

class ElasticClient
{
    protected static ?ElasticClient $elasticClient = null;

    protected Client $client;
    protected array $query;
    protected string $index;
    protected array $options = [];

    public static function makeClient(array $params)
    {
        if (static::$elasticClient) {
            return static::$elasticClient;
        }

        if (!array_key_exists('ELASTIC_URL', $params) || !$params['ELASTIC_URL']) {
            throw new ClientHostNotSpecifiedException("не указан хост elastic");
        }

        if (!array_key_exists('ELASTIC_INDEX', $params) || !$params['ELASTIC_INDEX']) {
            throw new ClientTargetNotSpecifiedException("Не указан индекс elastic");
        }

        if (!array_key_exists('ELASTIC_USER', $params) || !$params['ELASTIC_USER']) {
            throw new ClientUserNotSpecifiedException("Не указан пользователь elastic");
        }

        if (!array_key_exists('ELASTIC_PASSWORD', $params) || !$params['ELASTIC_PASSWORD']) {
            throw new ClientPasswordNotSpecifiedException("Не указан пароль elastic");
        }

        return new ElasticClient($params);

    }

    protected function __construct(array $params)
    {
        $this->client = ClientBuilder::create()->setHosts([$params['ELASTIC_URL']])->setBasicAuthentication($params['ELASTIC_USER'], $params['ELASTIC_PASSWORD'])
            ->build();

        $this->index = $params['ELASTIC_INDEX'];
    }


    public function query(array $params): Elasticsearch
    {
        $query = 
        [   'index' => $this->index,
            'body' => $params
        ];  

        $response = $this->client->search($query);
        return $response;


    }   


}