<?php
declare(strict_types=1);
namespace App\Model\Client;

use App\Exception\Client\DsnNotSpecifiedException;
use App\Exception\Client\PasswordNotSpecifiedException;
use App\Exception\Client\UserNotSpecifiedException;

class ClientConnectionValue
{
    private string $dsn;

    private string $user;

    private string $password;

    public static function makeInstance(string $dsn, string $user, string $password): ClientConnectionValue
    {
        return new ClientConnectionValue($dsn, $user, $password);
    }

    public static function makeInstanceFromArray(array $client_params): ClientConnectionValue
    {
        if (!isset($client_params['DSN']) || !is_string($client_params['DSN'])) {
            throw new DsnNotSpecifiedException("Не указан dsn подключения");
        }
        if (!isset($client_params['DB_USER']) || !is_string($client_params['DB_USER'])) {
            throw new UserNotSpecifiedException("Пользователь бд не указан");
        }
        if (!isset($client_params['DB_PASSWORD']) || !is_string($client_params['DB_PASSWORD'])) {
            throw new PasswordNotSpecifiedException("Не указан пароль бд");
        }

        return static::makeInstance($client_params['DSN'], $client_params['DB_USER'], $client_params['DB_PASSWORD']);
    }

    public function __construct(string $dsn, string $user, string $password)
    {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }



    
}