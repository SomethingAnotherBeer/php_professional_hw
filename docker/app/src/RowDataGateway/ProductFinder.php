<?php
declare(strict_types=1);
namespace App\RowDataGateway;

use App\Client\ClientConnection;
use App\Trait\ProductRowGatewayHydrateTrait;

class ProductFinder
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
     * @return ProductGateway[] $product_gateway_list
     */
    public function all(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products LIMIT :query_limit OFFSET :query_offset";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':query_limit' => $limit, ':query_offset' => $offset]);

        $product_params_list = $stmt->fetchAll();

        /** @var ProductGateway[] $product_gateway_list */
        $product_gateway_list = $this->hydrateProductList($this->clientConnection, $product_params_list);
                
        return $product_gateway_list;
    }
    
    public function findById(int $product_id): ?ProductGateway
    {   
        $product = null;
        
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products WHERE product_id = :product_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':product_id' => $product_id]);
        
        $product_params = $stmt->fetch();

        if (count($product_params) > 0) {
            $product = $this->hydrateProduct($this->clientConnection, $product_params);
        }

        return $product;
    }

    public function findByCategoryId(int $category_id, int $limit = 10, int $offset = 0): ProductGateway
    {
        $product = null;

        $sql = "SELECT p.product_id, p.category_id, p.product_name, p.product_price, p.in_stock FROM products AS p
            INNER JOIN categories AS c ON p.category_id = c.category_id WHERE c.category_id = :category_id LIMIT :query_limit OFFSET :query_offset";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id, ':query_limit' => $limit, ':query_offset' => $offset]);

        $product_params = $stmt->fetch();

        if (count($product_params) > 0) {
            $product = $this->hydrateProduct($this->clientConnection, $product_params);
        }

        return $product;

    }


}