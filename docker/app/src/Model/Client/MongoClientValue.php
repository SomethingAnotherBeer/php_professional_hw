<?php
declare(strict_types=1);
namespace App\Model\Client;


use App\Exception\Client\Mongo\MongoDbCannotBeEmptyException;
use App\Exception\Client\Mongo\MongoDbNotSpecifiedException;
use App\Exception\Client\Mongo\MongoHostCannotBeEmptyException;
use App\Exception\Client\Mongo\MongoHostNotSpecifiedException;
use App\Exception\Client\Mongo\MongoPasswordCannotBeEmptyException;
use App\Exception\Client\Mongo\MongoPasswordNotSpecifiedException;
use App\Exception\Client\Mongo\MongoPortCannotBeLessThanZeroException;
use App\Exception\Client\Mongo\MongoPortNotSpecifiedException;
use App\Exception\Client\Mongo\MongoUserCannotBeEmptyException;
use App\Exception\Client\Mongo\MongoUserNotSpecifiedException;

class MongoClientValue
{
    private string $mongo_user;

    private string $mongo_password;

    private string $mongo_host;

    private int $mongo_port;

    private string $mongo_db;

    public function __construct(string $mongo_user, string $mongo_password, string $mongo_host, string $mongo_db, int $mongo_port)
    {
        $this->checkMongoUser($mongo_user);
        $this->checkMongoPassword($mongo_password);
        $this->checkMongoHost($mongo_host);
        $this->checkMongoDb($mongo_db);
        $this->checkMongoPort($mongo_port);


        $this->mongo_user = $mongo_user;
        $this->mongo_password = $mongo_password;
        $this->mongo_host = $mongo_host;
        $this->mongo_db = $mongo_db;
        $this->mongo_port = $mongo_port;
    }

    public static function makeInstance(string $mongo_user, string $mongo_password, string $mongo_host, string $mongo_db, int $mongo_port): MongoClientValue
    {
        return new MongoClientValue($mongo_user, $mongo_password, $mongo_host, $mongo_db, $mongo_port);
    }

    public static function makeInstanceFromArray(array $mongo_params): MongoClientValue
    {
        if (!isset($mongo_params['MONGO_USER']) || !is_string($mongo_params['MONGO_USER'])) {
            throw new MongoUserNotSpecifiedException("Не указан пользователь mongo");
        }

        if (!isset($mongo_params['MONGO_PASSWORD']) || !is_string($mongo_params['MONGO_PASSWORD'])) {
            throw new MongoPasswordNotSpecifiedException("Не указан пароль mongo");
        }

        if (!isset($mongo_params['MONGO_HOST']) || !is_string($mongo_params['MONGO_HOST'])) {
            throw new MongoHostNotSpecifiedException("Не указан хост mongo");
        }

        if (!isset($mongo_params['MONGO_DB']) || !is_string($mongo_params['MONGO_DB'])) {
            throw new MongoDbNotSpecifiedException("Не указана база данных mongo");
        }

        if (!isset($mongo_params['MONGO_PORT']) || !is_int($mongo_params['MONGO_PORT'])) {
            throw new MongoPortNotSpecifiedException("Не указан порт mongo");
        }

        return static::makeInstance(
            $mongo_params['MONGO_USER'], 
            $mongo_params['MONGO_PASSWORD'], 
            $mongo_params['MONGO_HOST'],
            $mongo_params['MONGO_DB'],
            $mongo_params['MONGO_PORT']
            );
    }


    public function getMongoUser(): string
    {
        return $this->mongo_user;
    }

    public function getMongoPassword(): string
    {
        return $this->mongo_password;
    }

    public function getMongoHost(): string
    {
        return $this->mongo_host;
    }

    public function getMongoDb(): string
    {
        return $this->mongo_db;
    }
    
    public function getMongoPort(): int
    {
        return $this->mongo_port;
    }

    private function checkMongoUser(string $mongo_user): void
    {
        if ('' === trim($mongo_user)) {
            throw new MongoUserCannotBeEmptyException("Пользователь mongo не может иметь пустое значение");
        }
    }

    private function checkMongoPassword(string $mongo_password): void
    {
        if ('' === trim($mongo_password)) {
            throw new MongoPasswordCannotBeEmptyException("Пароль mongo не может быть пустым");
        }
    }

    private function checkMongoHost(string $mongo_host): void
    {
        if ('' === trim($mongo_host)) {
            throw new MongoHostCannotBeEmptyException("Хост mongo не может иметь пустое значение");
        }
    }

    private function checkMongoDb(string $mongo_db): void
    {
        if ('' === trim($mongo_db)) {
            throw new MongoDbCannotBeEmptyException("База данных mongo не может иметь пустое значение");
        }
    }

    private function checkMongoPort(int $mongo_port): void
    {
        if ($mongo_port < 0) {
            throw new MongoPortCannotBeLessThanZeroException("Порт mongo не может иметь значение меньше нуля");
        }
    }

}