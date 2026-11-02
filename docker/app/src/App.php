<?php
declare(strict_types=1);
namespace App;


use App\Client\ClientConnection;
use App\Constant\Load;
use App\DataMapper\CategoryMapper;
use App\DataMapper\ProductMapper;
use App\Model\Client\ClientConnectionValue;
use DI\ContainerBuilder;
use App\DataMapper\Category;
use App\DataMapper\Product;
use App\Exception\Product\ProductNotFoundException;

class App
{
    public function run(array $params)
    {   

        try{

            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addDefinitions([
            ClientConnection::class => function() use ($params) {
                $clientConnectionValue = ClientConnectionValue::makeInstanceFromArray($params);
                $clientConnection = ClientConnection::makeInstance($clientConnectionValue);
                return $clientConnection;
            }
            ]);

            $container = $containerBuilder->build();

            $categoryMapper = $container->get(CategoryMapper::class);
            $productMapper = $container->get(ProductMapper::class);

            $clientConnection = $container->get(ClientConnection::class);


            /** @var Category[] $category_list */
            $category_list = [];

            /** @var Product[] $product_list */
            $product_list = [];

            try {
                $clientConnection->getConnection()->beginTransaction();

                for ($i = 0; $i < 10; $i++) {
                    $category_name = "category_name " . $i + 1;
                    $category_list[] = $categoryMapper->create($category_name);
                }

                foreach ($category_list as $category) {
                    for ($i = 0; $i < 5; $i++) {
                        $category_id = $category->getCategoryId();
                        $product_name =  "product_name" . $i + $category->getCategoryId();
                        $product_price = (float)rand(1000, 5000);
                        $in_stock = $i % 2 === 0 && $i !== 0 ? true : false;

                        $product_list[] = $productMapper->create($category_id, $product_name, $product_price, $in_stock);

                    }
                }

                $clientConnection->getConnection()->commit();
            }
            catch (\Exception $e) {
                $clientConnection->getConnection()->rollBack();
                throw $e;
            }

            echo "Список категорий\n";
            array_walk($category_list, function(Category $category) {echo $category;});

            echo "Список продуктов\n";
            array_walk($product_list, function(Product $product) {echo $product;});

            $category = array_find($category_list, fn(Category $category) => "category_name 7" === $category->getCategoryId());

            var_dump($category->getProductList());

            $category_list_two = $categoryMapper->all(Load::EAGER);

            $fromSecondCategory = array_find($category_list_two, fn(Category $category) => "category_name 7" === $category->getCategoryId());

            if ($category !== $fromSecondCategory) {
                throw new \Exception("Ссылки категорий указывают на разные объекты, что-то не так с реализацией DataMapper");
            }


            $category->setCategoryName("category_name 77");

            if (!$fromSecondCategory->getCategoryName() !== "category_name 77") {
                throw new \Exception("Ошибка в строке 93");
            }

            var_dump($category->getProductList());

            $second_product_list = $productMapper->all();

            $productFromFirstList = array_find($product_list, fn(Product $product) => "product_name 7" === $product->getProductName());
            $productFromSecondList = array_find($second_product_list, fn(Product $product) => "product_name 7" === $product->getProductName());

            if ($productFromFirstList !== $productFromSecondList) {
                throw new \Exception("Ссылки продуктов указывают на разные объекты, что-то не так с реализацией DataMapper");
            }

            $categoryMapper->update($category);
            $productMapper->update($productFromFirstList);

            $productMapper->delete($productFromFirstList);

            try {
                $productMapper->update($productFromFirstList);
            }
            catch(ProductNotFoundException $e) {
                echo $e->getMessage() . "\n";
            }

            $currentCategory = array_find($category_list, fn(Category $category) => $category->getCategoryName() === "category_name 1");

            $categoryMapper->delete($currentCategory);
            
            $product_list_by_deleted_category = array_filter($product_list, fn(Product $product) => $product->getCategoryId() === $currentCategory->getCategoryId());
            $exception_message_list = [];

            try {
                foreach ($product_list_by_deleted_category as $deletedProduct) {
                    $productMapper->delete($deletedProduct);
                }
            }
            catch(ProductNotFoundException $e) {
                $exception_message_list[] = $e->getMessage();
            }

            $exception_message_list_str = implode("\n", $exception_message_list);
            echo $exception_message_list_str;

            echo "Выполнение успешно завершено\n";

            return 0;
        }

        catch(\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Ошибка выполнения\n";
            return 1;
        }

        
    }

}