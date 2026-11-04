<?php
declare(strict_types=1);
namespace App\Model\Client;

use App\Exception\Client\Elastic\ElasticHostCannotBeEmptyException;
use App\Exception\Client\Elastic\ElasticHostNotSpecifiedException;
use App\Exception\Client\Elastic\ElasticIndexCannotBeEmptyException;
use App\Exception\Client\Elastic\ElasticIndexNotSpecifiedException;
use App\Exception\Client\Elastic\ElasticPasswordCannotBeEmptyException;
use App\Exception\Client\Elastic\ElasticPasswordNotSpecifiedException;
use App\Exception\Client\Elastic\ElasticUserCannotBeEmptyException;
use App\Exception\Client\Elastic\ElasticUserNotSpecifiedException;

class ElasticClientValue
{
    private string $elastic_host;

    private string $elastic_index;

    private string $elastic_user;

    private string $elastic_password;

    public function __construct(string $elastic_host, string $elastic_index, string $elastic_user, string $elastic_password)
    {
        $this->checkElasticHost($elastic_host);
        $this->checkElasticIndex($elastic_index);
        $this->checkElasticUser($elastic_user);
        $this->checkElasticPassword($elastic_password);

        $this->elastic_host = $elastic_host;
        $this->elastic_index = $elastic_index;
        $this->elastic_user = $elastic_user;
        $this->elastic_password = $elastic_password;

    }


    public function getElasticHost(): string
    {
        return $this->elastic_host;
    }

    public function getElasticIndex(): string
    {
        return $this->elastic_index;
    }

    public function getElasticUser(): string
    {
        return $this->elastic_user;
    }

    public function getElasticPassword(): string
    {
        return $this->elastic_password;
    }


    private function checkElasticHost(string $elastic_host): void
    {
        if ('' === trim($elastic_host)) {
            throw new ElasticHostCannotBeEmptyException("Хост elastic не может быть пустым");
        }
    }

    private function checkElasticIndex(string $elastic_index): void
    {
        if ('' === trim($elastic_index)) {
            throw new ElasticIndexCannotBeEmptyException("Индекс elastic не может быть пустым");
        }
    }

    private function checkElasticUser(string $elastic_user): void
    {
        if ('' === trim($elastic_user)) {
            throw new ElasticUserCannotBeEmptyException("Пользователь elastic не может быть пустым");
        }
    }

    private function checkElasticPassword(string $elastic_password): void
    {
        if ('' === trim($elastic_password)) {
            throw new ElasticPasswordCannotBeEmptyException("Пароль elastic не может быть пустым");
        }
    }


    public static function makeInstance(string $elastic_host, string $elastic_index, string $elastic_user, string $elastic_password): ElasticClientValue
    {
        return new ElasticClientValue($elastic_host, $elastic_index, $elastic_user, $elastic_password);
    }


    public static function makeInstanceFromArray(array $elastic_params): ElasticClientValue
    {
        if (!isset($elastic_params['ELASTIC_HOST']) || !is_string($elastic_params['ELASTIC_HOST'])) {
            throw new ElasticHostNotSpecifiedException("Не указан хост elastic");
        }

        if (!isset($elastic_params['ELASTIC_INDEX']) || !is_string($elastic_params['ELASTIC_INDEX'])) {
            throw new ElasticIndexNotSpecifiedException("Не указан индекс elastic");
        }

        if (!isset($elastic_params['ELASTIC_USER']) || !is_string($elastic_params['ELASTIC_USER'])) {
            throw new ElasticUserNotSpecifiedException("Не указан пользователь elastic");
        }

        if (!isset($elastic_params['ELASTIC_PASSWORD']) || !is_string($elastic_params['ELASTIC_PASSWORD'])) {
            throw new ElasticPasswordNotSpecifiedException("Не указан пароль elastic");
        }

        return static::makeInstance($elastic_params['ELASTIC_HOST'], $elastic_params['ELASTIC_INDEX'], $elastic_params['ELASTIC_USER'], $elastic_params['ELASTIC_PASSWORD']);
    }


}