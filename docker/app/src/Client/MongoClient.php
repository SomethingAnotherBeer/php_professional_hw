<?php
declare(strict_types=1);
namespace App\Client;

use App\Model\Client\MongoClientValue;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

class MongoClient
{
    private static ?MongoClient $mongoClient = null;

    private Client $client;

    private Database $database;

    private Collection $currentCollection;

     public static function makeInstance(MongoClientValue $mongoClientValue): MongoClient
    {
        if (!static::$mongoClient) {
            static::$mongoClient = new MongoClient($mongoClientValue);
        }
        return static::$mongoClient;
    }

    protected function __construct(MongoClientValue $mongoClientValue)
    {
        $mongo_user = $mongoClientValue->getMongoUser();
        $mongo_password = $mongoClientValue->getMongoPassword();
        $mongo_host = $mongoClientValue->getMongoHost();
        $mongo_port = $mongoClientValue->getMongoPort();

        $this->client = new Client("mongodb://{$mongo_user}:{$mongo_password}@{$mongo_host}:{$mongo_port}");

        $this->database = $this->client->selectDatabase($mongoClientValue->getMongoDb());
        $this->currentCollection = $this->client->selectCollection($mongoClientValue->getMongoDb(), $mongoClientValue->getMongoCollection());

    }


    public function changeCollection(string $collection_name): Collection
    {
        $this->currentCollection = $this->client->selectCollection($this->database->getDatabaseName(), $collection_name);
        return $this->currentCollection;
    }

    public function getCollection(): Collection
    {
        return $this->currentCollection;
    }


}