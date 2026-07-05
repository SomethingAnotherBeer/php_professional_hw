<?php
declare(strict_types=1);
namespace App\Infrastructure;

use PDO;

class Connection
{
    private static ?\PDO $connection = null;

    public static function makeInstance(): Connection
    {
        return new Connection();
    }

    public function initializedConnection(array $connection_params): static
    {
        if (!static::$connection) {
            $db_host = $connection_params['db_host'];
            $db_name = $connection_params['db_name'];
            $db_user = $connection_params['db_user'];
            $db_password = $connection_params['db_password'];

            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            static::$connection = $pdo;
        }

        return $this;
    }

    public function getConnection(): ?\PDO
    {
        return static::$connection;
    }

}