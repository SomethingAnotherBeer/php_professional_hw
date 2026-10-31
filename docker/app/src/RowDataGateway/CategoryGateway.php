<?php
declare(strict_types=1);
namespace App\RowDataGateway;

use App\Client\ClientConnection;
use App\Interface\SerialIdInterface;

class CategoryGateway implements SerialIdInterface
{
    public const TABLE_NAME = 'categories';
    public const ID_NAME = 'category_id';

    private ClientConnection $clientConnection;

    public readonly int $category_id;

    public string $category_name;
    
    /** @var ProductGateway[] $product_list */
    public array $product_list;

    public static function create(ClientConnection $clientConnection, string $category_name): CategoryGateway
    {
        $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";

        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_name' => $category_name]);

        $category_id = $clientConnection->getLastConnectionId(static);
        $category_id = (int)$category_id;

        return static::makeInstance($clientConnection, $category_id, $category_name);
    }

    /**
     * @param ClientConnection $clientConnection
     * @param int $category_id
     * @param string $category_name
     * @param ProductGateway[] $product_list
     * @return CategoryGateway
     */
    public static function makeInstance(ClientConnection $clientConnection, int $category_id, string $category_name, array $product_list = [])
    {
        return new CategoryGateway($clientConnection, $category_id, $category_name, $product_list);
    }

    public function __construct(ClientConnection $clientConnection, int $category_id, string $category_name, array $product_list = [])
    {
        $this->clientConnection = $clientConnection;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->product_list = $product_list;
    }

    public function update(): static
    {
        $query_args = [];
        $category_params =
        [       
            'category_name' => $this->category_name,
        ];

        $query_args[] = "SET category_name = :category_name";
        $binded[':category_name'] = $category_params['category_name'];
        $binded[':category_id'] = $this->category_id;

        $query_args_str = implode(', ', $query_args);
        $sql = "UPDATE categories $query_args_str WHERE category_id = :category_id";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        return $this;

    }

    public function delete(): static
    {
        $sql = "DELETE FROM categories WHERE category_id = :category_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute(['category_id' => $this->category_id]);

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

    public function __toString(): string
    {
        $category_params =
        [
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
        ];
        $str_category_params = [];

        foreach ($category_params as $key => $value) {
            $str_category_params[] = "{$key}: {$value}";
        }       
        $category_params_str = implode("\n", $str_category_params);
        return $category_params_str;
    }
}