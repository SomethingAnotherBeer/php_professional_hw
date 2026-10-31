<?php
declare(strict_types=1);
namespace App\Service;

use App\Client\ClientConnection;
use App\Interface\ServiceStrategyInterface;
use App\RowDataGateway\CategoryFinder;
use App\RowDataGateway\ProductFinder;
use App\RowDataGateway\CategoryGateway;
use App\RowDataGateway\ProductGateway;

use Override;

class RowDataGatewayService implements ServiceStrategyInterface
{
    private CategoryFinder $categoryFinder;

    private ProductFinder $productFinder;

    private ClientConnection $clientConnection;

    public function __construct(CategoryFinder $categoryFinder, ProductFinder $productFinder, ClientConnection $clientConnection)
    {
        $this->categoryFinder = $categoryFinder;
        $this->productFinder = $productFinder;
        $this->clientConnection = $clientConnection;
    }

    public function process(): void
    {
        echo "Создадим 5 категорий\n";

        $category_gateway_list = $this->createAndGetCategories();
        echo "Текущий список категорий\n";
        $this->printCategoryGatewayList($category_gateway_list);


        $product_gateway_list = $this->createAndGetProductsByCategories($category_gateway_list);
            
        echo "Текущий список продуктов\n";
        $this->printProductGatewayList($product_gateway_list);

        echo "Посмотрим, есть ли в базе созданные категории и продукты\n";

        $from_source_category_list = $this->categoryFinder->findAll(5, 0);
        $from_source_product_list = $this->productFinder->findAll(50, 0);

        echo "Список категорий из базы\n";
        $this->printCategoryGatewayList($from_source_category_list);

        echo "Список продуктов из базы\n";
        $this->printProductGatewayList($from_source_product_list);

        echo "Обновим наименование категорий, добавим к каждой n\n";
        $this->updateCategoryList($category_gateway_list);

        echo "Посмотрим на обновленный список категорий\n";
        $this->printCategoryGatewayList($category_gateway_list);

        echo "Обновим продукты, добавив к наименованию каждого n, увеличив стоимость на 100 и инвертировав наличие\n";
        $this->updateProductList($product_gateway_list);

        echo "Посмотрим на обновленный список продуктов";
        $this->printProductGatewayList($product_gateway_list);

        echo "Удалим все продукты\n";
        


    }


    /**
     * @return CategoryGateway[]
     */
    protected function createAndGetCategories(): array
    {

        $category_list =
        [
            [
                'category_name' => 'category_name_1',
            ],

            [
                'category_name' => 'category_name_2',
            ],
            [
                'category_name' => 'category_name_3',
            ],
            [
                'category_name' => 'category_name_4',
            ],
            [
                'category_name' => 'category_name_5',
            ],
        ];

        /** @var CategoryGateway[] $category_gateway_list */
        $category_gateway_list = [];

        try {
            $this->clientConnection->getConnection()->beginTransaction();


            foreach ($category_list as $category_params) {
                $category_gateway_list[] = CategoryGateway::create($this->clientConnection, $category_params['category_name']);
            }


            $this->clientConnection->getConnection()->commit();
        }
        catch(\Exception $e) {
            $this->clientConnection->getConnection()->rollBack();
            throw $e;
        }

        return $category_gateway_list;
    }

    /**
     * @param CategoryGateway[] $category_gateway_list
     * @return ProductGateway[]
     */
    protected function createAndGetProductsByCategories(array $category_gateway_list): array
    {       
        $category_id_list = array_map(fn(CategoryGateway $categoryGateway) => $categoryGateway->category_id, $category_gateway_list);
        $product_params_list = [];

        /** @var ProductGateway[] $product_gateway_list */
        $product_gateway_list = [];

        foreach ($category_id_list as $category_id) {
            for ($i = 0; $i < 5; $i++ ) {
                $product_params =
                [
                    'category_id' => $category_id,
                    'product_name' => "category_name_" . $category_id + $i,
                    'product_price' => (float)rand(100, 5000),
                    'in_stock' => $i % 2 && $i !== 0 ? false : true, 
                ];
                $product_params_list[] = $product_params;

            }
        }
        
        try {
            $this->clientConnection->getConnection()->beginTransaction();

            foreach ($product_params_list as $product_params) {
                $product_gateway_list[] = ProductGateway::create($this->clientConnection,
                $product_params['category_id'],
                $product_params['product_name'],
                $product_params['product_price'],
                $product_params['in_stock']);
            }


            $this->clientConnection->getConnection()->commit();
        }
        catch(\Exception $e) {
            $this->clientConnection->getConnection()->rollBack();
            throw $e;

        }

        return $product_gateway_list;

    }

    /**
     * @param CategoryGateway[] $category_gateway_list
     */
    protected function updateCategoryList(array $category_gateway_list): void
    {
        try {
            $this->clientConnection->getConnection()->beginTransaction();

            foreach ($category_gateway_list as $categoryGateway) {
                $categoryGateway->category_name = $categoryGateway->category_name . "n";
                $categoryGateway->update();
            }

            $this->clientConnection->getConnection()->commit();
        }
        catch(\Exception $e) {
            $this->clientConnection->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @param ProductGateway[] $product_gateway_list
     */
    protected function updateProductList(array $product_gateway_list): void
    {
        try {
            $this->clientConnection->getConnection()->beginTransaction();

            foreach ($product_gateway_list as $productGateway) {
                $productGateway->product_name.="n";
                $productGateway->product_price+=100.0;
                $productGateway->in_stock = !$productGateway->in_stock;

                $productGateway->update();
            }


            $this->clientConnection->getConnection()->commit();
        }
        catch(\Exception $e) {
            $this->clientConnection->getConnection()->rollBack();
            throw $e;
        }
    }


    /**
     * @param CategoryGateway[] $category_gateway_list
     */
    protected function printCategoryGatewayList(array $category_gateway_list): void
    {   
        $string_category_list = array_map(fn(CategoryGateway $categoryGateway) => (string)$categoryGateway, $category_gateway_list);
        $string_category_list_str = implode("\n\n", $string_category_list);

        echo $string_category_list_str;
    }

    /** 
     * @param ProductGateway[] $product_gateway_list
     */
    protected function printProductGatewayList(array $product_gateway_list): void
    {
        $string_product_list = array_map(fn(ProductGateway $productGateway) => (string)$productGateway, $product_gateway_list);
        $string_product_list_str = implode("\n\n", $string_product_list);

        echo $string_product_list_str;
    }


}