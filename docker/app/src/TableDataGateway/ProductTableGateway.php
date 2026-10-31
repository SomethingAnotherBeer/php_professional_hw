<?php
declare(strict_types=1);
namespace App\TableDataGateway;

use App\Client\ClientConnection;
use App\Exception\Product\UndefinedProductIdException;
use PDO;

class ProductTableGateway
{


    private ClientConnection $clientConnection;

    public function __construct(ClientConnection $clientConnection)
    {
        $this->clientConnection = $clientConnection;
    }

    public function all(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products LIMIT :query_limit, OFFSET :query_offset LIMIT :query_limit OFFSET :query_offset";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':query_limit' => $limit, ':query_offset' => $offset]);

        $products = $stmt->fetchAll();

        return $products;
    }

    public function allByCategoryId(int $category_id, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.product_id, p.category_id, p.product_name, p.product_price, p.in_stock FROM products AS p
            INNER JOIN categories AS c ON p.category_id = c.category_id WHERE c.category_id = :category_id LIMIT :query_limit OFFSET :query_offset";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id, ':query_limit' => $limit, ':query_offset' => $offset]);

        $products = $stmt->fetchAll();

        return $products;
    }

    /**
     * @param int[] $product_id_list
     * @param int $limit
     * @param int $offset
     */
    public function allByProductIdIn(array $product_id_list, int $limit, int $offset): array
    {
        $placeholder_list = [];
        $bind_list = [];

        $product_id_list = array_filter($product_id_list, fn(mixed $product_value) => is_int($product_value));
        $product_id_list = array_values($product_id_list);

        foreach ($product_id_list as $product_id) {
            $placeholder = ":product_id_{$product_id}";
            $placeholder_list[] = $placeholder;
            $bind_list[$placeholder] = $product_id;
        }

        $placeholder_list_str = implode(", ", $placeholder_list);

        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock WHERE product_id IN ($placeholder_list_str) LIMIT :query_limit OFFSET :query_offset";
        $bind_list[':query_limit'] = $limit;
        $bind_list[':query_offset'] = $offset;

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($bind_list);

        $products = $stmt->fetchAll();

        return $products;


    }

    public function count()
    {

    }


    public function insertMany(array $product_params_list): int
    {
        $count = 0;

        $checked_params_list = [];
        foreach ($product_params_list as $product_params) {
            if (is_array($product_params)) {
                if (isset($product_params['product_name']) && is_string($product_params['product_name'])
                    && isset($product_params['category_id']) && is_int($product_params['category_id'])
                    && isset($product_params['product_price']) && is_float($product_params['product_price'])
                    && isset($product_params['in_stock']) && is_bool($product_params['in_stock'])) {
                        $checked_params_list[] = $product_params;
                    }
            }

        }

        $placeholder_list = [];
        $value_list = [];

        foreach ($checked_params_list as $checked_params) {
            $placeholder_list[] = '(?, ?, ?, ?)';

            $value_list[] = $checked_params['category_id'];
            $value_list[] = $checked_params['product_name'];
            $value_list[] = $checked_params['product_price'];
            $value_list[] = $checked_params['in_stock'];
        }

        if (count($value_list) > 0) {
            $placeholder_list_str = implode(', ', $placeholder_list);
            $sql = "INSERT INTO products (category_id, product_name, product_price, in_stock) VALUES {$placeholder_list_str}";
            
            $stmt = $this->clientConnection->getConnection()->prepare($sql);
            $stmt->execute($value_list);
            
            $count = $stmt->rowCount();
        }

        return $count;

    }
    
   
    public function updateMany(array $product_params_list): int
    {
        $connection = $this->clientConnection->getConnection();

        $product_params_list = array_filter($product_params_list, fn(mixed $params) => is_array($params) && isset($params['product_id']) && is_int($params['product_id']));
        $product_id_list = array_map(fn(array $params) => $params['product_id'], $product_params_list);

        $finded_product_id_list = $this->getFoundedProductIdListByProductIdIn($product_id_list);
        $undefined_product_id_list = array_diff($product_id_list, $finded_product_id_list);

        if (count($undefined_product_id_list) > 0) {
            $undefined_product_id_list = implode(", ", $undefined_product_id_list);
            throw new UndefinedProductIdException("Обнаружены следующие несуществующие в системе идентификаторы продуктов: {$undefined_product_id_list}");
        }

        $updated_row_count = 0;

        try {
            $connection->beginTransaction();

            foreach ($product_params_list as $product_params) {
                $bind_list = [];
                $update_query_list = [];

                if (isset($product_params['product_name']) && is_string($product_params['product_name'])) {
                    $bind_list[':product_name'] = $product_params['product_name'];
                    $update_query_list[] = "SET product_name = :product_name";
                }
                if (isset($product_params['product_price']) && is_float($product_params['product_price'])) {
                    $bind_list[':product_price'] = $product_params['product_price'];
                    $update_query_list[] = "SET product_price = :product_price";
                }
                if (isset($product_params['in_stock']) && is_bool($product_params['in_stock'])) {
                    $bind_list[':in_stock'] = $product_params['in_stock'];
                    $update_query_list[] = "SET in_stock = :in_stock";
                }

                if (count($update_query_list) > 0) {
                    $update_query_list_str = implode(", ", $update_query_list);
                    $sql = "UPDATE products {$update_query_list_str} WHERE product_id = :product_id";
                    $bind_list[':product_id'] = $product_params['product_id'];  
                    
                    $stmt = $connection->prepare($sql);
                    $stmt->execute($bind_list);

                    $updated_row_count+= $stmt->rowCount();
                    
                }

        }


            $connection->commit();
        }
        catch(\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $updated_row_count;

    }


    /** 
     * @param int[] $product_id_list
     * @return int
     */
    public function deleteMany(array $product_id_list): int
    {
        if (count($product_id_list) === 0) {
            return 0;
        }    

        $deleted_count = 0;

        $product_id_list = array_values($product_id_list);
        $placeholder_id_str = str_repeat('?', count($product_id_list) - 1) . '?';

        $sql = "DELETE FROM products WHERE product_id IN ($placeholder_id_str)";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($product_id_list);

        $deleted_count = $stmt->rowCount();

        return $deleted_count;

    }


    /**
     * @param int[] $product_id_list
     * @return int[] 
     */
    protected function getFoundedProductIdListByProductIdIn(array $product_id_list): array
    {
        $founded_id_list = [];
        $product_id_list = array_values($product_id_list);

        if (count($product_id_list) > 0){
            $product_id_list_str = str_repeat('?', count($product_id_list) - 1) . '?';
            $sql = "SELECT product_id FROM products WHERE product_id IN $product_id_list_str";
            $stmt = $this->clientConnection->getConnection()->prepare($sql);
            $stmt->execute($product_id_list);

            $founded_id_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $founded_id_list;
    }


}