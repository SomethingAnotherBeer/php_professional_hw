<?php
declare(strict_types=1);
namespace App\DataMapper;
use App\Client\ClientConnection;
use App\Exception\DataMapper\UndefiendEntityException;
use App\Exception\Product\ProductNotFoundException;
use App\IdentityMap\IdentityMap;

class ProductMapper
{
    public const TABLE_NAME = 'products';

    public const ID_NAME = 'product_id';

    private ClientConnection $clientConnection;
    
    private IdentityMap $identityMap;

    public function __construct(ClientConnection $clientConnection, IdentityMap $identityMap)
    {
        $this->clientConnection = $clientConnection;
        $this->identityMap = $identityMap;
    }


    public function create(
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
            ':in_stock' => (int)$in_stock
        ];

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        $product_id = (int)$this->clientConnection->getLastConnectionId(static::TABLE_NAME, static::ID_NAME);

        $product = Product::makeInstance($product_id, $category_id, $product_name, $product_price, $in_stock);

        $this->identityMap->set($product);

        return $product;

    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Product[]
     */
    public function all(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products LIMIT :limit OFFSET :offset";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':limit' => $limit, ':offset' => $offset]);

        $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product = Product::makeInstance(
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);

                $identifier = Product::class . ':' . $product->getProductId();
                $this->identityMap->set($product);

                $product_list[] = $this->identityMap->get($identifier);
                
            }
        }

        return $product_list;

    }


     /**
     * @return Product[]
     */
    public function allByCategoryId(int $category_id): array
    {
        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products WHERE category_id = :category_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product = Product::makeInstance(
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);

                $identifier = Product::class . ':' . $product->getProductId();
                $this->identityMap->set($product);

                $product_list[] = $this->identityMap->get($identifier);
            }
        }

        return $product_list;
    }


    /**
     * @param int[] $category_id_list
     * @return Product[]
     */
    public function allByCategoryIdIn(array $category_id_list): array
    {
        $placeholder_list_str = implode(', ', array_fill(0, count($category_id_list), '?'));
        $category_id_list = array_values($category_id_list);

        $sql = "SELECT product_id, category_id, product_name, product_price, in_stock FROM products WHERE category_id IN ($placeholder_list_str)";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($category_id_list);

         $product_params_list = $stmt->fetchAll();

        /** @var Product[] $product_list */
        $product_list = [];

        if ($product_params_list) {
            foreach ($product_params_list as $product_params) {
                $product = Product::makeInstance(
                (int)$product_params['product_id'],
                (int)$product_params['category_id'],
                $product_params['product_name'],
                (float)$product_params['product_price'],
                (bool)$product_params['in_stock']);

                $identifier = Product::class . ':' . $product->getProductId();
                $this->identityMap->set($product);
                $product_list[] = $this->identityMap->get($identifier);
            }
        }

        return $product_list;

    }


    public function update(Product $product): Product
    {
        $identifier = Product::class . ':' . $product->getProductId();

        if (!$this->identityMap->has($identifier)) {
            throw new UndefiendEntityException("Неизвестная сущность продукта");
        }

        if ($product->isDeleted()) {
            throw new ProductNotFoundException("Продукт не найден");
        }

        $sql = "UPDATE products SET product_name = :product_name, product_price = :product_price, in_stock = :in_stock WHERE product_id = :product_id";
        $binded =
        [
            ':product_id' => $product->getProductId(),
            ':product_name' => $product->getProductName(),
            ':product_price' => $product->getProductPrice(),
            ':in_stock' => (int)$product->isInStock(),
        ];

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($binded);

        $this->identityMap->set($product);

        return $product;


    }

    public function delete(Product $product): static
    {
        $identifier = Product::class . ':' . $product->getProductId();

        if (!$this->identityMap->has($identifier)) {
            throw new UndefiendEntityException("Неизвестная сущность продукта");
        }

        if ($product->isDeleted()) {
            throw new ProductNotFoundException("Продукт не найден");
        }

        $sql = "DELETE FROM products WHERE product_id = :product_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':product_id' => $product->getProductId()]);
    
        $product->setDeleted();

        return $this;
    }




}