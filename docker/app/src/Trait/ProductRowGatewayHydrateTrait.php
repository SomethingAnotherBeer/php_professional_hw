<?php
declare(strict_types=1);
namespace App\Trait;

use App\Client\ClientConnection;
use App\RowDataGateway\ProductGateway;

trait ProductRowGatewayHydrateTrait
{
    /**
     * @param ClientConnection $clientConnection
     * @param array $product_params_list
     * @return ProductGateway[]
     */
    protected function hydrateProductList(ClientConnection $clientConnection, array $product_params_list)
    {
        /** @var ProductGateway[] $product_gateway_list */
        $product_gateway_list = [];

        foreach ($product_params_list as $product_params) {
            $product = $this->hydrateProduct($clientConnection, $product_params);
            if (null !== $product) {
                $product_gateway_list[$product->product_id] = $product;
            }
        }

        return $product_gateway_list;


    }

    protected function hydrateProduct(ClientConnection $clientConnection, array $product_params): ?ProductGateway
    {
        $product = null;

        $checkers =
        [
            'product_id' => fn(mixed $product_id) => is_numeric($product_id),
            'category_id' => fn(mixed $category_id) => is_int($category_id),
            'product_name' => fn(mixed $product_name) => is_string($product_name),
            'product_price' => fn(mixed $product_price) => is_numeric($product_price),
            'in_stock' => fn(mixed $in_stock) => (is_int($in_stock) && (1 === $in_stock || 0 === $in_stock)) || (is_bool($in_stock)),
        ];
        $handlers =
        [
            'product_id' => fn(mixed $product_id) => (int)$product_id,
            'product_price' => fn(mixed $product_price) => (float)$product_price,
            'in_stock' => fn(mixed $in_stock) => (bool)$in_stock,
        ];

        $is_checked = false;


        if (count(array_intersect_key($product_params, $checkers)) === count($checkers)) {
            $checked_count = 0;
            foreach ($product_params as $product_param_key => $product_param_value) {
                if (true === $checkers[$product_param_key]($product_param_value)) {
                    $checked_count+=1;
                }
            }

            if ($checked_count = count($checkers)) {
                $is_checked = true;
            }

        }

        if ($is_checked) {
            foreach ($product_params as $product_param_key => $product_param_value) {
                if (isset($handlers[$product_param_key])) {
                    $product_params[$product_param_key] = $handlers($product_param_key)($product_param_value);
                }
            }

            $product = ProductGateway::makeInstance
            (
                $clientConnection,
                $product_params['product_id'],
                $product_params['category_id'],
                $product_params['product_name'],
                $product_params['product_price'],
                $product_params['in_stock'],
            );

        }

        return $product;

    }

}