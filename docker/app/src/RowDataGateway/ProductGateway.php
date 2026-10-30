<?php
declare(strict_types=1);
namespace App\RowDataGateway;

use App\Client\ClientConnection;
use App\Interface\SerialIdInterface;
use Override;

class ProductGateway implements SerialIdInterface
{
    public const TABLE_NAME = 'products';
    public const ID_NAME = 'product_id';

    private ClientConnection $clientConnection;

    public int $product_id;

    public int $category_id;

    public string $product_name;

    public float $product_price;

    public bool $in_stock;

    public static function create
    (
        ClientConnection $clientConnection,
        int $category_id,
        string $product_name,
        float $product_price,
        bool $in_stock
    ): ProductGateway
    {
        $sql = "INSERT INTO products (category_id, product_name, product_price, in_stock) VALUES (:category_id, :product_name, :product_price, :in_stock)";
        $binded =
        [
            ':category_id' => $category_id,
            ':product_name' => $product_name,
            ':product_price' => $product_price,
            ':in_stock' => $in_stock
        ];

        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        $product_id = $clientConnection->getLastConnectionId(static);
        $product_id = (int)$product_id;

        return static::makeInstance($clientConnection, $product_id, $category_id, $product_name, $product_price, $in_stock);

    }

    public static function makeInstance
    (
        ClientConnection $clientConnection,
        int $product_id,
        int $category_id,
        string $product_name,
        float $product_price,
        bool $in_stock
    ): ProductGateway
    {
        return new ProductGateway($clientConnection, $product_id, $category_id, $product_name, $product_price, $in_stock);
    }


    public function __construct
    (
        ClientConnection $clientConnection,
        int $product_id,
        int $category_id,
        string $product_name,
        float $product_price,
        bool $in_stock
    )
    {
        $this->clientConnection = $clientConnection;
        $this->product_id = $product_id;
        $this->category_id = $category_id;
        $this->product_name = $product_name;
        $this->product_price = $product_price;
        $this->in_stock = $in_stock;
    }



    public function update(array $params): static
    {   
        $query_args = [];
        $binded = [];

        $sql = "UPDATE products";

        if (isset($params['product_name']) && is_string($params['product_name'])) {
            $query_args[] = "SET product_name = :product_name";
            $binded[':product_name'] = $params[':product_name'];
        }
        if (isset($params['product_price']) && is_float($params['product_price'])) {
            $query_args[] = "SET product_price = :product_price";
            $binded[':product_price'] = $params['product_price'];
        }
        if (isset($params['in_stock']) && is_bool($params['in_stock'])) {
            $query_args[] = "SET in_stock = :in_stock";
            $binded[':in_stock'] = $params[':in_stock'];
        }

        if (count($query_args) > 0) {
            $sql .= " " . implode(", ", $query_args);
            $stmt = $this->clientConnection->getConnection()->prepare($sql);
            $stmt->execute($binded);

            $this->updateGateway($params);
        }

        return $this;

    }

    public function delete(): static
    {
        $sql = "DELETE FROM products WHERE product_id = :product_id";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':product_id' => $this->product_id]);

        return $this;
    }

    
    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getIdName(): string
    {
        return static::ID_NAME;
    }

    protected function updateGateway(array $params): static
    {
        if (isset($params['product_name']) && is_string($params['product_name'])) {
            $this->product_name = $params['product_name'];
        }
        if (isset($params['product_price']) && is_float($params['product_price'])) {
            $this->product_price = $params['product_price'];
        }
        if (isset($params['in_stock']) && is_bool($params['in_stock'])) {
            $this->in_stock = $params['in_stock'];
        }

        return $this;
    }


}