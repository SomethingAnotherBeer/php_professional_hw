<?php
declare(strict_types=1);
namespace App\Client;

use App\Model\Client\ClientConnectionValue;
use PDO;

class ClientConnection
{
    private static ?ClientConnection $clientConnection = null;

    private PDO $pdo;

    public static function makeInstance(ClientConnectionValue $clientConnectionValue)
    {
        if (!static::$clientConnection){
            static::$clientConnection = new ClientConnection($clientConnectionValue);
        }
        return static::$clientConnection;
    }

    protected function __construct(ClientConnectionValue $clientConnectionValue)
    {
        try {
            $this->pdo = new PDO($clientConnectionValue->getDsn(), $clientConnectionValue->getUser(), $clientConnectionValue->getPassword());

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(\PDOException $e) {
            throw $e;
        }
    
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getLastConnectionId(string $table_name, string $id_name): ?string
    {
        $attribute = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $last_id = null;

        switch($attribute) {
            case 'pgsql':
                $last_id = $this->pdo->lastInsertId("{$table_name}_{$id_name}_seq");
            break;
            case 'mysql':
                $last_id = $this->pdo->lastInsertId();
            break;
        }

        return $last_id;
    }


    


}