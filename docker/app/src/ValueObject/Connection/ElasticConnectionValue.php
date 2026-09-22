<?php
declare(strict_types=1);
namespace App\ValueObject\Connection;

class ElasticConnectionValue
{
    protected string $elastic_url;

    protected string $elastic_index;

    protected string $elastic_user;

    protected string $elastic_password;

    public function __construct(array $params)
    {
        $this->elastic_url = $params['elastic_url'];
        $this->elastic_index = $params['elastic_index'];
        $this->elastic_user = $params['elastic_user'];
        $this->elastic_password = $params['elastic_password'];
    }

    public function getElasticUrl(): string
    {
        return $this->elastic_url;
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

    
}