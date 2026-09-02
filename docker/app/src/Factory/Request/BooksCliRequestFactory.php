<?php
declare(strict_types=1);
namespace App\Factory\Request;

use App\Exception\Request\RequestFieldNotSpecifiedException;
use App\Exception\Request\RequestFieldTypeException;
use App\Request\BooksRequest;

class BooksCliRequestFactory
{
    public function makeBooksRequest(array $params): BooksRequest
    {
        foreach ($params as $param_key => $param_value) {
            $params[$param_key] = trim($param_value);
        }
        $params = array_filter($params, fn(string $param) => '' !== $param);
        $prepared_params = [];

        if (isset($params['category']) && '' !== $params['category']) {
            $prepared_params['category'] = $params['category'];
        }
        
        if (isset($params['book_name'])) {
            $prepared_params['book_name'] = $params['book_name'];
        }

        if (isset($params['category'])) {
            $prepared_params['category'] = $params['category'];
        }

        if (isset($params['book_price_from'])) {
            $prepared_params['book_price_from'] = $this->prepareAndGetPriceFrom($params['book_price_from']);
        }

        if (isset($params['book_price_to'])) {
            $prepared_params['book_price_to'] = $this->prepareAndGetPriceTo($params['book_price_to']);
        }

        if (isset($params['in_stock'])) {
            $prepared_params['in_stock'] = $this->prepareAndGetInStock($params['in_stock']);
        }

        if (isset($params['from'])) {
            $prepared_params['from'] = $this->prepareAndGetFrom($params['from']);
        }

        if (isset($params['size'])) {
            $prepared_params['size'] = $this->prepareAndGetSize($params['size']); 
        }

        return BooksRequest::make($prepared_params);
    }

    protected function prepareAndGetPriceFrom(string $book_price_from): float
    {
        if (!is_numeric($book_price_from)) {
            throw new RequestFieldTypeException("Нижняя граница стоимости товара имеет нечисловой тип");
        }
        return (float)$book_price_from;
    }

    protected function prepareAndGetPriceTo(string $book_price_to): float
    {
        if (!is_numeric($book_price_to)) {
            throw new RequestFieldTypeException("Верхняя граница стоимости товара имеет нечисловой тип");
        }
        return (float)$book_price_to;
    }

    protected function prepareAndGetInStock(string $in_stock): bool
    {
        $available_in_stock_params =
        [   
            'yes' => true,
            'no' => false,
            'y' => true,
            'n' => false,
        ];

        if (!isset($available_in_stock_params[$in_stock])) {
            throw new RequestFieldTypeException("Параметр \"в наличии\" имеет некорректное значение");
        }
        return $available_in_stock_params[$in_stock];

    }

    protected function prepareAndGetFrom(string $from): int
    {
        return (is_numeric($from)) ? (int)$from : 0;
    }

    protected function prepareAndGetSize(string $size): int
    {
        return (is_numeric($size)) ? (int)$size : 100;
    }


}