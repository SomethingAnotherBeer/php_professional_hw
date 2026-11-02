<?php
declare(strict_types=1);
namespace App\DataMapper;

use App\Client\ClientConnection;
use App\Constant\Load;
use App\Exception\Category\CategoryNotFoundException;
use App\Exception\DataMapper\UndefiendEntityException;
use App\IdentityMap\IdentityMap;

class CategoryMapper
{
    public const TABLE_NAME = "products";
    public const ID_NAME = "product_id";

    private ClientConnection $clientConnection;
    
    private IdentityMap $identityMap;

    private ProductMapper $productMapper;

    public function __construct(
        ClientConnection $clientConnection,
        IdentityMap $identityMap, 
        ProductMapper $productMapper)
    {
        $this->clientConnection = $clientConnection;
        $this->identityMap = $identityMap;
        $this->productMapper = $productMapper;
    }

    public function all(int $load = Load::LAZY, int $limit = 50, int $offset = 0)
    {
        $sql = "SELECT category_id, category_name FROM categories LIMIT :limit OFFSET :offset";

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
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
            $product_list = $this->productMapper->allByCategoryIdIn($category_id_list);

            foreach ($product_list as $product) {
                if (isset($category_params_by_id_list[$product->getCategoryId()])) {
                    $category_params_by_id_list[$product->getCategoryId()][] = $product;
                }
            }
            
        }

        foreach ($category_params_by_id_list as $category_params) {
            $identifier = static::class . ":" . $category_params['category_id'];
            $this->identityMap->set(Category::makeInstance((int)$category_params['category_id'], $category_params['category_name'], $category_params['product_list']));

            $category_list[] = $this->identityMap->get($identifier);
        }



        return $category_list;

    }


    public function findById(int $category_id, int $load = Load::LAZY): ?Category
    {
        $category = null;
        $identifier = static::class . ":" . $category_id;

        if ($this->identityMap->has($identifier)) {
            $category = $this->identityMap->get($identifier);
            return $category;
        }

        $sql = "SELECT category_id, category_name FROM categories WHERE category_id = :category_id";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);

        $category_params = $stmt->fetch();

        if ($category_params) {
            if (Load::EAGER === $load) {
                $product_list = $this->productMapper->allByCategoryId($category_id);
                $category_params['product_list'] = $product_list;
            }

            $category = Category::makeInstance((int)$category_params['category_id'], $category_params['category_name'], $category_params['product_list']);

            $this->identityMap->set($category);
        }

        return $this->identityMap->get($identifier);

    }


     public function create(string $category_name): Category
     {   
        
        $sql = "INSERT INTO categories (category_name) VALUES (:category_name)";
        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute([':category_name' => $category_name]);
        $category_id = (int)$this->clientConnection->getLastConnectionId(static::TABLE_NAME, static::ID_NAME);

        $category = Category::makeInstance($category_id, $category_name, []);

        $this->identityMap->set($category);

        return $category;

    }


    public function update(Category $category): Category
    {
        $identifier = static::class . ':' . $category->getCategoryId();
        
        if (!$this->identityMap->has($identifier)) {
            throw new UndefiendEntityException("Неизвестная сущность категории");
        }

        if ($category->isDeleted()) {
            throw new CategoryNotFoundException("Категория не найдена");
        }

        $sql = "UPDATE categories SET category_name = :category_name WHERE category_id = :category_id";
        $bind_list = [':category_name' => $category->getCategoryName(), ':category_id' => $category->getCategoryid()];

        $stmt = $this->clientConnection->getConnection()->prepare($sql);
        $stmt->execute($bind_list);

        $this->identityMap->set($category);
    
        return $category;


    }

    public function delete(Category $category): Category
    {
        $identifier = static::class . ":" . $category->getCategoryId();

        if (!$this->identityMap->has($identifier)) {
            throw new UndefiendEntityException("Неизвестная сущность категории");
        }

        if ($category->isDeleted()) {
            throw new CategoryNotFoundException("Категория не найдена");
        }

        $connection = $this->clientConnection->getConnection();

        try {
            $connection->beginTransaction();

            $product_list = $this->productMapper->allByCategoryId($category->getCategoryId());

            foreach ($product_list as $product) {
                $this->productMapper->delete($product);
            }

            $sql = "DELETE FROM categories WHERE category_id = :category_id";
            $stmt= $this->clientConnection->getConnection()->prepare($sql);
            $stmt->execute([':category_id' => $category->getCategoryid()]);

            $category->setDeleted();

            $connection->commit();
        }
        catch(\Exception $e) {
            $connection->rollBack();
            throw $e;
        }



        return $category;
    }


    


}