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

    public function all(array $query_params = [], int $limit = 10, int $offset = 0): array
    {
        $condition_params = $this->getConditionParams($query_params);
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products";

        $binded = [];
        $condition_list = [];

        if (count($condition_params['binded']) > 0) {
            $binded = $condition_params['binded'];
        }
        if (count($condition_params['condition_list']) > 0) {
            $condition_list = $condition_params['condition_list'];
            $condition_list_str = implode(" AND ", $condition_list);
            $sql.= " WHERE $condition_list_str";
        }

        $binded[':query_limit'] = $limit;
        $binded[':query_offset'] = $offset;
        $sql.= " LIMIT :query_limit OFFSET :query_offset";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        $products = $stmt->fetchAll();

        return $products;

    }

    public function allByCategoryId(int $category_id, array $condition_params = [], int $limit = 10, int $offset = 0): array
    {
        $prefix_list =
        [
            'category_id' => 'c',
            'product_name' => 'p',
            'product_price' => 'p',
            'in_stock' => 'p',
        ];
        $condition_params['category_id'] = $category_id;
        $condition_params = $this->getConditionParams($condition_params, $prefix_list);

        $sql = "SELECT p.product_id, p.category_id, p.product_name, p.product_price, p.in_stock FROM products AS p 
        INNER JOIN categories AS c ON p.category_id = c.category_id";

        $binded = $condition_params['binded'];

        $condition_list = $condition_params['condition_list'];
        $condition_list_str = implode(" AND ", $condition_list);
        $sql.= " WHERE $condition_list_str";

        $sql.= " LIMIT :query_limit OFFSET :query_offset";

        $binded[':query_limit'] = $limit;
        $binded[':query_offset'] = $offset;

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        $products = $stmt->fetchAll();

        return $products;

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



    protected function getConditionParams(array $condition_params, array $prefix_list = []): array
    {
        $binded = [];
        $condition_arg_list = [];

        $column_list =
        [
            'category_id' => 'category_id',
            'product_name' => 'product_name',
            'product_price' => 'product_price',
            'in_stock' => 'in_stock',
        ];

        foreach ($column_list as $column_key => $column_value) {
            if (isset($prefix_list[$column_key])) {
                $column_list[$column_key] = "{$prefix_list[$column_key]}.$column_value";
            }
        }

        if (isset($condition_params['category_id']) && is_int($condition_params['category_id'])) {
            $binded[':category_id'] = $condition_params['category_id'];
            $condition_arg_list[] = "{$column_list['category_id']} = :category_id";
        }

        if (isset($condition_params['product_name_eq']) && is_string($condition_params['product_name_eq'])) {
            $binded[':product_name_eq'] = $condition_params['product_name_eq'];
            $condition_arg_list[] = "{$column_list['product_name']} = :product_name_eq";
        }
        if (isset($condition_params['product_price_lte']) && is_float($condition_params['product_price_lte'])) {
            $binded[':product_price_lte'] = $condition_params['product_price_lte'];
            $condition_arg_list[] = "{$column_list['product_price_lte']} <= :product_price_lte";
        }
        if (isset($condition_params['product_price_gte']) && is_float($condition_params['product_price_gte'])) {
            $binded[':product_price_gte'] = $condition_params['product_price_gte'];
            $condition_arg_list[] = "{$column_list['product_price_gte']} >= :product_price_gte";
        }
        if (isset($condition_params['in_stock']) && is_bool($condition_params['in_stock'])) {
            $binded[':in_stock'] = $condition_params['in_stock'];
            $condition_arg_list[] = "{$column_list['in_stock']} = :in_stock";
        }

        return 
        [
            'binded' => $binded,
            'condition_list' => $condition_arg_list,    
        ];

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