<?php
declare(strict_types=1);
namespace App\ActiveRecord;

use App\Client\ClientConnection;
use App\Exception\Product\ProductNotFoundException;

class Product
{
    public const TABLE_NAME = "products";
    public const ID_NAME = "product_id";


    private ClientConnection $clientConnection;

    public readonly int $product_id;

    public readonly int $category_id;

    public string $product_name;

    public float $product_price;

    public bool $in_stock;

    private bool $isExists = true;

    public function __construct(
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


    public static function makeInstance(ClientConnection $clientConnection,
    int $product_id,
    int $category_id,
    string $product_name,
    float $product_price,
    bool $in_stock
    ): Product
    {
        return new Product($clientConnection, $product_id, $category_id, $product_name, $product_price, $in_stock);
    }


    public static function create(ClientConnection $clientConnection,
    int $category_id,
    string $product_name,
    float $product_price,
    bool $in_stock): Product
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

        $product_id = (int)$clientConnection->getLastConnectionId(static::TABLE_NAME, static::ID_NAME);

        return static::makeInstance($clientConnection, $product_id, $category_id, $product_name, $product_price, $in_stock);

    }


    /**
     * @param ClientConnection $clientConnection
     * @param int $limit
     * @param int $offset
     * @return Product[]
     */
    public static function all(ClientConnection $clientConnection, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products LIMIT :limit OFFSET :offset";
        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':limit' => $limit, ':offset' => $offset]);

        $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product_list[] = static::makeInstance($clientConnection, 
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);
            }
        }

        return $product_list;

    }

     /**
     * @param ClientConnection $clientConnection
     * @return Product[]
     */
    public static function allByCategoryId(ClientConnection $clientConnection, int $category_id): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products WHERE category_id = :category_id LIMIT :limit OFFSET :offset";
        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product_list[] = static::makeInstance($clientConnection, 
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);
            }
        }

        return $product_list;
    }

    /**
     * @param ClientConnection $clientConnection
     * @param int[] $category_id_list
     * @return Product[]
     */
    public static function allByCategoryIdIn(ClientConnection $clientConnection, array $category_id_list): array
    {
        $placeholder_list_str = str_repeat('?', count($category_id_list) - 1) . '?';
        $category_id_list = array_values($category_id_list);

        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products WHERE category_id IN ($placeholder_list_str)";
        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute($category_id_list);

         $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product_list[] = static::makeInstance($clientConnection, 
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);
            }
        }

        return $product_list;



    }

    
    public function update(): static
    {
        if (!$this->isExists) {
            throw new ProductNotFoundException("Продукт не найден");
        }

        $sql = "UPDATE products SET product_name = :product_name, product_price = :product_price, in_stock = :in_stock WHERE product_id = :product_id";
        $binded =
        [
            ':product_id' => $this->product_id,
            ':product_name' => $this->product_name,
            ':product_price' => $this->product_price,
            ':in_stock' => $this->in_stock
        ];

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        return $this;


    }

    public function delete(): static
    {
        if (!$this->isExists) {
            throw new ProductNotFoundException("Продукт не найден");
        }

        $sql = "DELETE FROM products WHERE product_id = :product_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':product_id' => $this->product_id]);
        
        $this->setIsNotExists();

        return $this;
    }


    protected function setIsNotExists(): void
    {
        $this->isExists = false;
    }



}