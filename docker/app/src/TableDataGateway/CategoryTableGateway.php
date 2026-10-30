<?php
declare(strict_types=1);
namespace App\TableDataGateway;

use App\Client\ClientConnection;
use App\Exception\Category\UndefinedCategoryIdException;
use PDO;

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


    public function insertMany(array $category_list): int
    {
        $connection = $this->clientConnection->getConnection();
        
        $category_list = array_filter($category_list, fn(mixed $params) => is_array($params) && isset($params['category_name']) && is_string($params['category_name']));
        $values = [];
        $placeholder_params = [];

        foreach ($category_list as $category_params) {
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

    public function updateMany(array $category_params_list): int
    {
        $connection = $this->clientConnection->getConnection();
        $category_params_list = array_filter($category_params_list, fn(mixed $params) => is_array($params) && isset($params['category_id']) && is_int($params['category_id']));

        $category_id_list = array_map(fn(array $params) => $params['category_id'], $category_params_list);
        $finded_category_id_list = $this->getCategoryIdListByCategoryIdList($category_id_list);

        $undefined_category_id_list = array_diff($category_id_list, $finded_category_id_list);
        if (count($undefined_category_id_list) > 0) {
            $undefined_category_id_list_str = implode(', ', $undefined_category_id_list);
            throw new UndefinedCategoryIdException("Обнаружены следующие несуществующие в системе идентификаторы категорий: {$undefined_category_id_list_str}");
        }

        $updated_row_count = 0;

        try {
            $connection->beginTransaction();

            foreach ($category_params_list as $category_params) {
                $update_query_list = [];
                $bind_list = [];

                if (isset($category_params['category_name']) && is_string($category_params['category_name'])) {
                    $update_query_list[] = "SET category_name = :category_name";
                    $bind_list[':category_name'] = $category_params['category_name'];
                }


                if (count($update_query_list) > 0) {
                    $update_query_list_str = implode(', ', $update_query_list);
                    $bind_list[':category_id'] = $category_params['category_id'];

                    $sql = "UPDATE categories {$update_query_list_str} WHERE category_id = :category_id";
                    
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



    public function searchByCategoryNameLike(string $category_name_like, int $limit = 10, int $offset = 0): array
    {   
        $connection = $this->clientConnection->getConnection();
        $sql = "SELECT category_id, category_name FROM categories WHERE category_name LIKE :category_name, LIMIT :query_limit, OFFSET => :query_offset";
        $stmt = $connection->prepare($sql);
        $stmt->execute([':category_name' =>"%$category_name_like%", ':query_limit' => $limit, ':query_offset' => $offset]);
        
        $categories = $stmt->fetchAll();

        return $categories;
    }

    /**
     * @param int[] $category_id_list
     * @return int
     */
    public function deleteMany(array $category_id_list): int
    {
        $connection = $this->clientConnection->getConnection();
        $category_id_list = array_values($category_id_list);

        $placeholder = str_repeat('?', count($category_id_list) - 1) . '?';

        $sql = "DELETE FROM categories WHERE category_id IN $placeholder";
        $stmt = $connection->prepare($sql);
        $stmt->execute($category_id_list);

        $count = $stmt->rowCount();

        return $count;

    }


    /**
     * @param int[] $category_id_list
     * @return int[]
     */
    protected function getCategoryIdListByCategoryIdList(array $category_id_list): array
    {
        $placeholder_list = str_repeat('?', count($category_id_list) - 1) . '?';
        $category_id_list = array_values($category_id_list);

        $sql = "SELECT category_id FROM categories WHERE category_id IN ($placeholder_list)";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($category_id_list);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }



}