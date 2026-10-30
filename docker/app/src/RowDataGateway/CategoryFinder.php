<?php
declare(strict_types=1);
namespace App\RowDataGateway;

use App\Client\ClientConnection;
use App\Trait\ProductRowGatewayHydrateTrait;

class CategoryFinder
{
    use ProductRowGatewayHydrateTrait;

    private ClientConnection $clientConnection;

    public function __construct(ClientConnection $clientConnection)
    {
        $this->clientConnection = $clientConnection;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @return CategoryGateway[]
     */
    public function findAll(int $limit = 10, int $offset = 0)
    {
        $sql = "SELECT category_id, category_name FROM categories LIMIT :query_limit OFFSET :query_offset";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);

        $stmt->execute([':query_limit' => $limit, ':query_offset' => $offset]);
        $categories = $stmt->fetchAll();

        $category_gateway_list = $this->hydrateList($categories);

        return $category_gateway_list;


    }

    public function findById(int $category_id): ?CategoryGateway
    {
        $sql = "SELECT category_id, category_name FROM categories WHERE category_id = :category_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);

        $stmt->execute([':category_id' => $category_id]);
        $category_params = $stmt->fetch();
        $category = null;

        if ($category_params) {
            $category = $this->hydrateCategory($category_params);
        }

        return $category;
    }

    /**
     * @param int $category_id
     * @return CategoryGateway
     */
    public function findByIdWithProducts(int $category_id): CategoryGateway
    {
        $sql = "SELECT c.category_id, c.category_name, p.product_id, p.product_name, p.product_price, p.in_stock
        FROM categories AS c LEFT JOIN products AS p ON c.category_id = p.category_id WHERE c.category_id = :category_id";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        $category_params = $stmt->fetchAll();
        $prepared_params = [];

        if ($category_params && isset($category_params[0])) {
            $prepared_params['category_id'] = $category_params[0]['category_id'];
            $prepared_params['category_name'] = $category_params[0]['category_name'];
            $prepared_params['product_list'] = [];

            foreach ($category_params as $category_product_params) {
                $product_params = [];
                if (isset($category_product_params['product_id'])) {
                    $product_params['product_id'] = $category_product_params['product_id'];
                    $product_params['category_id'] = $prepared_params['category_id'];
                    $product_params['product_name'] = $category_product_params['product_name'];
                    $product_params['product_price'] = $category_product_params['product_price'];
                    $product_params['in_stock'] = $category_product_params['in_stock'];

                    $prepared_params['product_list'][$product_params['product_id']] = $product_params;
                }
            }

        }
        return $this->hydrateCategory($prepared_params);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return CategoryGateway[]
     */
    public function findWithProducts(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT c.category_id, c.category_name, p.product_id, p.category_id AS product_category_id, p.product_name, p.product_price, p.in_stock
            FROM categories AS c LEFT JOIN products AS p ON c.category_id = p.category_id LIMIT :query_limit OFFSET :query_offset";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':query_limit' => $limit, ':query_offset' => $offset]);

        $category_params_list = $stmt->fetchAll();

        $prepared_params_list = [];

        if (count($category_params_list) > 0) {
            foreach ($category_params_list as $category_params) {
                $cid = $category_params['category_id'];

                if (!isset($prepared_params_list[$cid])) {
                    $prepared_params_list[$cid] = 
                    [
                        'category_id' => $category_params['category_id'],
                        'category_name' => $category_params['category_name'],
                        'product_list' => [],
                    ];
                }

                if (isset($category_params['product_id']) && isset($category_params['product_category_id'])) {
                    $prepared_params_list[$cid]['product_list']['product_id'] = $category_params['product_id'];
                    $prepared_params_list[$cid]['product_list']['category_id'] = $category_params['product_category_id'];
                    $prepared_params_list[$cid]['product_list']['product_name'] = $category_params['product_name'];
                    $prepared_params_list[$cid]['product_list']['product_price'] = $category_params['product_price'];
                    $prepared_params_list[$cid]['product_list']['in_stock'] = $category_params['in_stock'];
                }

                

            }
        }

        /** @var CategoryGateway[] $category_gateway_list */
        $category_gateway_list = $this->hydrateList($prepared_params_list);
        return $category_gateway_list;

    }



    /**
     * @param array $category_params_list
     * @return CategoryGateway[]
     */
    protected function hydrateList(array $category_params_list): array
    {   
        /** @var CategoryGateway[] $category_gateway_list */
        $category_gateway_list = [];

        foreach ($category_params_list as $category_params) {
            $category = $this->hydrateCategory($category_params);

            if (null !== $category) {
                $category_gateway_list[$category->category_id] = $category;
            }
        }
        
        return $category_gateway_list;

    }


    protected function hydrateCategory(array $category_params): ?CategoryGateway
    {
        $category = null;

        $checkers =
        [
            'category_id' => fn(mixed $category_id) => is_numeric($category_id),
            'category_name' => fn(mixed $category_name) => is_string($category_name),
            'product_list' => fn(mixed $product_list) => is_array($product_list),
        ];

        $handlers = 
        [
            'category_id' => fn(mixed $category_id) => (int)$category_id,
            'product_list' => fn(array $product_list) => $this->hydrateProductList($this->clientConnection, $product_list),
        ];

        $is_checked = false;

        if (count(array_intersect_key($category_params, $checkers)) === count($checkers)
            || ( count(array_intersect_key($category_params, $checkers)) === count($checkers) - 1 && !isset($category_params['product_list'])) ) {

            $checked_count = 0;
            foreach ($category_params as $category_param_key => $category_param_value) {
                if (true === $checkers[$category_param_key]($category_param_value)) {
                    $checked_count+=1;
                }
                if ($checked_count === count($checkers)) {
                    $is_checked = true;
                }
            }
        
        }

        if ($is_checked) {
            foreach ($category_params as $category_param_key => $category_param_value) {
                if (isset($handlers[$category_param_key])) {
                    $category_params[$category_param_key] = $handlers[$category_param_key]($category_param_value);
                }
            }
            
            $category = CategoryGateway::makeInstance($this->clientConnection, $category_params['category_id'], $category_params['category_name'], $category_params['product_list'] ?? []);

        }

        return $category;

    }


}