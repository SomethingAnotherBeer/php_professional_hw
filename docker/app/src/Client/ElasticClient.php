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

class ElasticClient
{
    protected Client $client;
    protected string $index;
    protected array $options = [];

    public static function makeClient(array $params)
    {
        if (!array_key_exists('elastic_host', $params) || !$params['elastic_host']) {
            throw new ClientHostNotSpecifiedException("не указан хост elastic");
        }

        if (!array_key_exists('elastic_index', $params) || !$params['elastic_index']) {
            throw new ClientTargetNotSpecifiedException("Не указан индекс elastic");
        }

        if (!array_key_exists('elastic_user', $params) || !$params['elastic_user']) {
            throw new ClientUserNotSpecifiedException("Не указан пользователь elastic");
        }

        if (!array_key_exists('elastic_password', $params) || !$params['elastic_password']) {
            throw new ClientPasswordNotSpecifiedException("Не указан пароль elastic");
        }

        return new ElasticClient($params);

    }

    public function __construct(array $params)
    {
        $this->client = ClientBuilder::create()->setHosts($params['elastic_host'])->setBasicAuthentication($params['elastic_user'], $params['elastic_password'])
            ->build();

        $this->index = $params['elastic_index'];

        if (array_key_exists('fuzziness', $params)) {
            $available_fuzziness_values = [0, 1, 2, 'AUTO'];
            if (in_array($params['fuzziness'], $available_fuzziness_values)) {
                $this->options['fuzziness'] = $params['fuzziness'];
            }
        }
    }

    public function query(string $query_string)
    {
        $query_args = $this->prepareAndGetQueryString($query_string);
        
    }

    private function prepareAndGetQueryString(string $query_string): array
    {
        $query_args = json_decode($query_string);
        if (null === $query_args) {
            throw new InvalidQueryException("Некорректный формат запроса");
        }

        return $query_args;
    }


}