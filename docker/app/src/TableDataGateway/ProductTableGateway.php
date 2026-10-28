<?php
declare(strict_types=1);
namespace App\TableDataGateway;

use App\Client\ClientConnection;

class ProductTableGateway
{
    private ClientConnection $clientConnection;

    public function __construct(ClientConnection $clientConnection)
    {
        $this->clientConnection = $clientConnection;
    }


    public function all(array $query_params = [], int $limit = 10, int $offset = 0): array
    {   
        $connection = $this->clientConnection->getConnection();

        $condition_params = $this->getConditionParams($query_params);
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM  products";
        $binded = [];
            
        if (count($condition_params['binded']) > 0) {
            $sql.= implode(", ", $condition_params['condition_args']);
            $binded = $condition_params['binded'];
        }

        $binded[':query_limit'] = $limit;
        $binded[':query_offset'] = $offset;

        $sql.= " LIMIT :query_limit OFFSET :query_offset";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute($binded);

        $products = $stmt->fetchAll();

        return $products;
        

    }

    public function allByCategoryId(int $category_id, array $query_params = [], int $limit = 10, int $offset = 0): array
    {
        $connection = $this->clientConnection->getConnection();

        $condition_params['category_id'] = $category_id;
        $condition_params = $this->getConditionParams($query_params);

        $sql = "SELECT p.product_id, p.category_id, p.product_name, p.product_price, p.in_stock FROM products AS p 
            INNER JOIN categories AS c ON p.category_id = c.category_id";
        $binded = $condition_params['binded'];

        $sql.= " " . implode(", ", $condition_params['condition_args']);

        $binded[':query_limit'] = $limit;
        $binded[':query_offset'] = $offset;

        $sql.= " LIMIT :query_limit OFFSET :query_offset";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute($binded);

        $products = $stmt->fetchAll();

        return $products;


    }

    public function update(array $update_params, array $condition_params = []): int
    {
        $connection = $this->clientConnection->getConnection();
        $condition_params = $this->getConditionParams($condition_params);

        $query_args = [];
        $binded = [];
        $count = 0;

        if (isset($update_params['product_name']) && is_string($update_params['product_name'])) {
            $query_args[] = "SET product_name = :product_name";
            $binded[':product_name'] = $update_params['product_name'];
        }
        if (isset($update_params['product_price']) && is_float($update_params['product_price'])) {
            $query_args[] = "SET product_price = :product_price";
            $binded[':product_price'] = $update_params['product_price'];
        }
        if (isset($update_params['in_stock']) && is_bool($update_params['in_stock'])) {
            $query_args[] = "SET in_stock = :in_stock";
            $binded[':in_stock'] = $update_params['in_stock'];
        }

        if (count($query_args) > 0) {
            $query_args_str = implode(', ', $query_args) . " ";
            $sql = "UPDATE products " . $query_args_str;

            if (count($condition_params['condition_args']) > 0) {
                $sql .= " " . implode(", ", $condition_params['condition_args']);
                $binded = array_merge($binded, $condition_params['binded']);
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute($binded);
            $count = $stmt->rowCount();
        }

        return $count;

    }

    public function updateByCategoryId(int $category_id, array $update_params, array $condition_params = [])
    {
        $connection = $this->clientConnection->getConnection();

        $condition_params['category_id'] = $category_id;
        $condition_params = $this->getConditionParams($condition_params);

        $query_args = [];
        $binded = [];
        $count = 0;

        if (isset($update_params['product_name']) && is_string($update_params['product_name'])) {
            $query_args[] = "SET product_name = :product_name";
            $binded[':product_name'] = $update_params['product_name'];
        }
        if (isset($update_params['product_price']) && is_float($update_params['product_price'])) {
            $query_args[] = "SET product_price = :product_price";
            $binded[':product_price'] = $update_params['product_price'];
        }
        if (isset($update_params['in_stock']) && is_bool($update_params['in_stock'])) {
            $query_args[] = "SET in_stock = :in_stock";
            $binded[':in_stock'] = $update_params['in_stock'];
        }

        if (count($query_args) > 0) {
            $query_args_str = implode(', ', $query_args) . " ";
            $sql = "UPDATE products " . $query_args_str;

            if (count($condition_params['condition_args']) > 0) {
                $condition_params['condition_args'][] = "AND category_id = :category_id";
                $sql .= " " . implode(", ", $condition_params['condition_args']);
                $binded = array_merge($binded, $condition_params['binded']);
            }

            $stmt = $connection->prepare($sql);
            $stmt->execute($binded);
            $count = $stmt->rowCount();
        }

        return $count;
    }

    public function delete(array $condition_params = []): int
    {
        $connection = $this->clientConnection->getConnection();
        $condition_params = $this->getConditionParams($condition_params);

        $sql = "DELETE FROM products";
        $binded = [];

        if (count($condition_params['condition_args']) > 0) {
            $sql.= " " . implode(", ", $condition_params['condition_args']);
            $binded = $condition_params['binded'];
        }

        $stmt = $connection->prepare($sql);
        $stmt->execute($binded);

        $count = $stmt->rowCount();

        return $count;

    }

    public function deleteByCategoryId(int $category_id, array $condition_params = []): int
    {
        $connection = $this->clientConnection->getConnection();
        $condition_params['category_id'] = $category_id;

        $condition_params = $this->getConditionParams($condition_params);

        $sql = "DELETE FROM products";
        $binded = $condition_params['binded'];

        $sql = " " . implode(", ", $condition_params['condition_args']);

        $stmt = $connection->prepare($sql);
        $stmt->execute($binded);

        $count = $stmt->rowCount();

        return $count;
    }



    private function getConditionParams(array $query_params)
    {
        $is_condition_init = false;
        $condition_args = [];
        $binded = [];

        if (isset($query_params['product_name_like']) && is_string($query_params['product_name_like'])) {
            $condition_args[] = $is_condition_init ? "WHERE product_name LIKE :product_name_like" : "AND product_name LIKE :product_name_like";

            $binded[':product_name_like'] = "%{$query_params['product_name_like']}%";
            $is_condition_init = true;
        }
        if (isset($query_params['product_price_from']) && is_float($query_params['product_price_from'])) {
            $condition_args[] = $is_condition_init ? "WHERE product_price >= :product_price_from" : "AND product_price >= :product_price_from";

            $binded[":product_price_from"] = $query_params['product_price_from'];
            $is_condition_init = true;
        }

        if (isset($query_params['product_price_to']) && is_float($query_params['product_price_to'])) {
            $condition_args[] = $is_condition_init ? "WHERE product_price <= :product_price_to" : "AND product_price <= :product_price_to";

            $binded[":product_price_to"] = $query_params['product_price_to'];
            $is_condition_init = true;
        }

        if (isset($query_params['in_stock_is']) && is_bool($query_params['in_stock_is'])) {
            $condition_args[] = $is_condition_init ? "WHERE in_stock = :in_stock_is" : "AND in_stock = :in_stock_is";
            $binded[':in_stock_is'] = $query_params['in_stock_is'];
            $is_condition_init = true;
        }

        $condition_params =
        [
            'condition_args' => $condition_args,
            'binded' => $binded,
        ];

        return $condition_params;
    }




}