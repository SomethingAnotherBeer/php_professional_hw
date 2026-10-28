<?php
declare(strict_types=1);
namespace App\TableDataGateway;

use App\Client\ClientConnection;

class CategoryTableGateway
{
    private ClientConnection $clientConnection;

    public function __construct(ClientConnection $clientConnection)
    {
        $this->clientConnection = $clientConnection;
    }


    public function all(int $limit = 10, int $offset = 0): array
    {
        $connection = $this->clientConnection->getConnection();

        $sql = "SELECT category_id, category_name FROM categories LIMIT :query_limit OFFSET :query_offset";
        $stmt = $connection->prepare($sql);
        $stmt->execute([':query_limit' => $limit, ':query_offset' => $offset]);

        $categories = $stmt->fetchAll();

        return $categories;
    }

    /** 
     * @param int[] $category_id_list
     * @param int $limit
     * @param int $offset
     */
    public function allByIdInList(array $category_id_list, int $limit = 10, int $offset = 0): array
    {
        $connection = $this->clientConnection->getConnection();

        $placeholder_id_list = [];
        
        foreach ($category_id_list as $index => $category_id) {
            $placeholder = "category_id:$index";
            $placeholder_id_list[$placeholder] = $category_id;
        }

        $placeholder_id_str = implode(", ", array_keys($placeholder_id_list));

        $sql = "SELECT category_id, category_name FROM categories WHERE category_id IN $placeholder_id_str LIMIT :query_limit OFFSET :query_offset";
        $stmt = $connection->prepare($sql);
        $stmt->execute(array_merge($placeholder_id_list, [':query_limit' => $limit, ':query_offset' => $offset]));

        $categories = $stmt->fetchAll();

        return $categories;
    }

    public function updateByIdList(array $category_id_list, array $params): int
    {
        $connection = $this->clientConnection->getConnection();

        $query_args = [];
        $binded = [];
        $count = 0;

        if (isset($params['category_name']) && is_string($params['category_name'])) {
            $query_args[] = "SET category_name = :category_name";
            $binded[':category_name'] = $params['category_name'];
        }

        if (count($binded) > 0) {
             $placeholder_id_list = [];
        
            foreach ($category_id_list as $index => $category_id) {
                $placeholder = "category_id:$index";
                $placeholder_id_list[$placeholder] = $category_id;
            }
            $placeholder_id_str = implode(", ", array_keys($placeholder_id_list));

            $sql = "UPDATE categories " . implode(", ", $query_args) . " WHERE category_id IN $placeholder_id_str";
            $stmt = $connection->prepare($sql);
            $stmt->execute(array_merge($binded, $placeholder_id_list));

            $count = $stmt->rowCount();
        }

        return $count;

    }

    public function updateAll(array $params): int
    {

        $connection = $this->clientConnection->getConnection();

        $query_args = [];

        $binded = [];
        $count = 0;

        if (isset($params['category_name']) && is_string($params['category_name'])) {
            $query_args[] = "SET category_name = :category_name";
            $binded[':category_name'] = $params['category_name'];
        }

        if (count($binded) > 0) {
            $sql = "UPDATE categories" . implode(', ', $query_args);
            $stmt = $connection->prepare($sql);
            $stmt->execute($binded);

            $count = $stmt->rowCount();
            
        }

        return $count;

    }


    public function insert(array $category_list): int
    {
        $connection = $this->clientConnection->getConnection();

        $checked_category_list = [];

        foreach ($category_list as $category_params) {
            $is_checked = true;

            if (!isset($category_params['category_name']) || !is_string($category_params['category_name'])) {
                $is_checked = false;
            }

            if ($is_checked) {
                $checked_category_list[] = $category_params;
            }

        } 

        $values = [];
        $placeholder_params = [];

        foreach ($checked_category_list as $category_params) {
            $placeholder_params[] = '(?)';

            $values[] = $category_params['category_name'];
        }

        $placeholder_params_str = implode(', ', $placeholder_params);
        $sql = "INSERT INTO categories (category_name) VALUES $placeholder_params_str";

        $stmt = $connection->prepare($sql);
        $stmt->execute($values);

        $count = $stmt->rowCount();

        return $count;


    }

    public function searchByCategoryNameLike(string $category_name_like, int $limit = 10, int $offset = 0): array
    {   
        $connection = $this->clientConnection->getConnection();
        $sql = "SELECT category_id, category_name FROM categories WHERE category_name LIKE :category_name, :query_limit => $limit, :query_offset => $offset";
        $stmt = $connection->prepare($sql);
        $stmt->execute([':category_name' =>"%$category_name_like%"]);
        
        $categories = $stmt->fetchAll();

        return $categories;
    }

    public function deleteById(array $category_id_list): int
    {
        $connection = $this->clientConnection->getConnection();

        $placeholder = str_repeat('?', count($category_id_list) - 1) . '?';

        $sql = "DELETE FROM categories WHERE category_id IN $placeholder";
        $stmt = $connection->prepare($sql);
        $stmt->execute($category_id_list);

        $count = $stmt->rowCount();

        return $count;

    }



}