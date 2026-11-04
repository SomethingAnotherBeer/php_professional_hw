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


            $clientConnectionValue = ClientConnectionValue::makeInstanceFromArray($params);
            $clientConnection = ClientConnection::makeInstance($clientConnectionValue);

            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addDefinitions([
            ClientConnection::class => function() use ($clientConnection) {
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
                    $category_name = "category_name " . ($i + 1);
                    $category_list[] = $categoryMapper->create($category_name);
                }

                for ($i = 0; $i < count($category_list); $i++) {
                    for ($j = 0; $j < 5; $j++) {
                        $category_id = $category_list[$i]->getCategoryId();
                        $product_name =  "product_name " . ($i + $j + 1);
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
            array_walk($category_list, function(Category $category) {echo $category . "\n______\n";});
            echo "\n\n";

            echo "Список продуктов\n";
            array_walk($product_list, function(Product $product) {echo $product . "\n______\n";});
            echo "\n\n";

            $category = array_find($category_list, fn(Category $category) => "category_name 7" === $category->getCategoryName());


            $category_list_two = $categoryMapper->all(Load::EAGER);

            $fromSecondCategory = array_find($category_list_two, fn(Category $category) => "category_name 7" === $category->getCategoryName());

            if ($category !== $fromSecondCategory) {
                throw new \Exception("Ссылки категорий указывают на разные объекты, что-то не так с реализацией DataMapper");
            }


            $category->setCategoryName("category_name 77");


            if ($fromSecondCategory->getCategoryName() !== "category_name 77") {
                throw new \Exception("Ошибка в присваивании по ссылке");
            }


            $second_product_list = $productMapper->all();

            $productFromFirstList = array_find($product_list, fn(Product $product) => "product_name 7" === $product->getProductName());
            $productFromSecondList = array_find($second_product_list, fn(Product $product) => "product_name 7" === $product->getProductName());

            if ($productFromFirstList !== $productFromSecondList) {
                throw new \Exception("Ссылки продуктов указывают на разные объекты, что-то не так с реализацией DataMapper");
            }

            echo "Обновляем категорию с наименованием category_name 77\n";
            $categoryMapper->update($category);
            
            echo "Обновляем продукт с наименованием product_name 77 из первого списка\n";
            $productFromFirstList->setProductName("product_name 77")->setProductPrice(5000);
            $productMapper->update($productFromFirstList);
           
            echo "Теперь удаляем этот продукт\n";
            $productMapper->delete($productFromFirstList);

            echo "Пытаемся обновить идентичный продукт из второго списка, полученного путем массового запроса данных\n";
            echo "Так как они в IdentityMap указывают на один и тот же объект, у нас не получится его обновить, вылетит ошибка: \n";
            try {
                $productMapper->update($productFromSecondList);
            }
            catch(ProductNotFoundException $e) {
                echo $e->getMessage() . "\n";
            }

            echo "Находим категорию с наименованием category_name 1 и удаляем ее\n";

            $currentCategory = array_find($category_list, fn(Category $category) => $category->getCategoryName() === "category_name 1");

            $categoryMapper->delete($currentCategory);
            
            $product_list_by_deleted_category = array_filter($product_list, fn(Product $product) => $product->getCategoryId() === $currentCategory->getCategoryId());
            $exception_message_list = [];

            echo "Далее выводим ошибки при попытке повторно удалить привязанные к удаленной категории продукты, взятые из product_list\n";
            foreach ($product_list_by_deleted_category as $deletedProduct) {
                try {
                    $productMapper->delete($deletedProduct);
                }
                catch(ProductNotFoundException $e) {
                    $exception_message_list[] = $e->getMessage();
                }
            }

            $exception_message_list_str = implode("\n", $exception_message_list);
            echo $exception_message_list_str . "\n";

            $this->clear($clientConnection);
            echo "Выполнение успешно завершено\n";
            
            return 0;
        }

        catch(\Exception $e) {
            $exception_params =
            [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];

            echo json_encode($exception_params, JSON_UNESCAPED_UNICODE);
            echo "Ошибка выполнения\n";
            return 1;
        }

        
    }


    protected function clear(ClientConnection $clientConnection): void
    {
        $connection = $clientConnection->getConnection();

        $delete_categories_sql = "DELETE FROM categories";
        $delete_products_sql = "DELETE FROM products";


        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare($delete_products_sql);
            $stmt->execute();

            $stmt = $connection->prepare($delete_categories_sql);
            $stmt->execute();


            $connection->commit();
        }
        catch(\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }


}