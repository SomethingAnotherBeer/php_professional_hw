<?php
declare(strict_types=1);
namespace App\ActiveRecord;

use App\Client\ClientConnection;
use App\Constant\Load;
use App\Exception\Category\CategoryNotFoundException;
use App\Interface\SerialIdInterface;

class Category
{
    public const TABLE_NAME = "categories";
    
    public const ID_NAME = "category_id";
    
    public readonly int $category_id;

    public string $category_name;

    /** @var Product[] $product_list */
    public readonly array $product_list;

    private bool $is_exists = true;

    private ClientConnection $clientConnection;

    public function __construct(ClientConnection $clientConnection, int $category_id, string $category_name, array $product_list = [])
    {
        $this->clientConnection = $clientConnection;

        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->product_list = $product_list;

    }

    public static function makeInstance(ClientConnection $clientConnection, int $category_id, string $category_name, array $product_list = []): Category
    {
        return new Category($clientConnection, $category_id, $category_name, $product_list);
    }

    /**
     * @param ClientConnection $clientConnection
     * @param int $load
     * @param int $limit
     * @param int $offset
     */
    public static function all(ClientConnection $clientConnection, int $load = Load::LAZY, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT category_id, category_name FROM categories LIMIT :limit OFFSET :offset";

        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':limit' => $limit, ':offset' => $offset]);

        $category_params_list = $stmt->fetchAll();
        $category_params_by_id_list = [];

        /** @var Category[] $category_list */
        $category_list = [];

        foreach ($category_params_list as $category_params) {
            $category_id = (int)$category_params['category_id'];
            $category_params_by_id_list[$category_id] = $category_params;
            $category_params_by_id_list[$category_id]['product_list'] = [];
        }

        if (Load::EAGER === $load) {
            $category_id_list = array_keys($category_params_by_id_list);
            $product_list = Product::allByCategoryIdIn($clientConnection, $category_id_list);

            foreach ($product_list as $product) {
                if (isset($category_params_by_id_list[$product->category_id])) {
                    $category_params_by_id_list[$product->category_id][] = $product;
                }
            }
            
        }

        foreach ($category_params_by_id_list as $category_params) {
            $category_list[] = Category::makeInstance($clientConnection, (int)$category_params['category_id'], $category_params['category_name'], $category_params['product_list']);
        }

        return $category_list;

    }

    public static function findById(ClientConnection $clientConnection, int $category_id, int $load = Load::LAZY): ?Category
    {
        $sql = "SELECT category_id, category_name FROM categories WHERE category_id = :category_id";
        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        $category = null;

        $category_params = $stmt->fetch();

        if ($category_params) {
            if (Load::EAGER === $load) {
                $product_list = Product::allByCategoryId($clientConnection, $category_id);
                $category_params['product_list'] = $product_list;
            }

            $category = Category::makeInstance($clientConnection, (int)$category_params['category_id'], $category_params['category_name'], $category_params['product_list']);
        }
        return $category;

    }

    public static function create(ClientConnection $clientConnection, string $category_name): Category
    {   
        
        $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
        $stmt = $clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_name' => $category_name]);
        $category_id = (int)$clientConnection->getLastConnectionId(static::TABLE_NAME, static::ID_NAME);

        return static::makeInstance($clientConnection, $category_id, $category_name, []);
        

    }

    public function save(): static
    {
        if (!$this->is_exists) {
            throw new CategoryNotFoundException("Категория не найдена");
        }

        $category_params = 
        [
            'category_name' => $this->category_name,
        ];

        $sql = "UPDATE categories SET category_name = :category_name WHERE category_id = :category_id";
        $bind_list = [':category_name' => $category_params['category_name'], ':category_id' => $this->category_id];

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($bind_list);

        return $this;


    }

    public function delete(): static
    {
        if (!$this->is_exists) {
            throw new CategoryNotFoundException("Категория не найдена");
        }

        $sql = "DELETE FROM categories WHERE category_id = :category_id";
        $stmt= $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $this->category_id]);

        $this->setNotExists();

        return $this;
    }
    

    protected function setNotExists(): void
    {
        $this->is_exists = false;
    }

}