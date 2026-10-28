<?php
declare(strict_types=1);
namespace App\Client;

use App\Model\Client\ClientConnectionValue;
use PDO;

class ClientConnection
{
    private static ?ClientConnection $clientConnection;

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

}