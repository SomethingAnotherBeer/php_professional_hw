<?php
declare(strict_types=1);
namespace App\Factory\Request;

use App\Exception\Request\RequestFieldNotSpecifiedException;
use App\Exception\Request\RequestFieldTypeException;
use App\Request\BooksRequest;

class BooksCliRequestFactory extends CliRequestFactory
{
    public function makeBooksRequest(array $params): BooksRequest
    {
        foreach ($params as $param_key => $param_value) {
            $params[$param_key] = trim($param_value);
        }
        $params = array_filter($params, fn(string $param) => '' !== $param);

        $prepared_params = [];
        $this->checkBookName('book_name', $params);
        $this->checkBookPriceFrom('book_price_from', $params);
        $this->checkBookPriceTo('book_price_to', $params);
        $this->checkInStock('in_stock', $params);

        $prepared_params['book_name'] = $params['book_name'];
        $prepared_params['book_price_from'] = $this->prepareAndGetPriceFrom('book_price_from', $params);
        $prepared_params['book_price_to'] = $this->prepareAndGetPriceTo('book_price_to', $params);
        $prepared_params['in_stock'] = $this->prepareAndGetInStock('in_stock', $params);

        if (isset($params['category']) && '' !== $params['category']) {
            $prepared_params['category'] = $params['category'];
        }

        return BooksRequest::make($prepared_params);
    }


    protected function checkBookName(string $book_name_key, array $params): void
    {
        if (!isset($params[$book_name_key]) || '' === trim($params[$book_name_key])) {
            throw new RequestFieldNotSpecifiedException("Не указано наименование книги");
        }
    }

    protected function checkBookPriceFrom(string $book_price_from_key, array $params): void
    {
        if (!isset($params[$book_price_from_key]) || '' === $params[$book_price_from_key]) {
            throw new RequestFieldNotSpecifiedException("Не указана нижняя граница стоимости книги");
        }
    }

    protected function checkBookPriceTo(string $book_price_to_key, array $params): void
    {
        if (!isset($params[$book_price_to_key]) || '' === $params[$book_price_to_key]) {
            throw new RequestFieldNotSpecifiedException("Не указана верхняя граница стоимости книги");
        }
    }

    protected function checkInStock(string $in_stock_key, array $params): void
    {
        if (!isset($paraams[$in_stock_key]) || '' === $params[$in_stock_key]) {
            throw new RequestFieldNotSpecifiedException("Не указан параметр \"в наличии\"");
        }
    }

    protected function prepareAndGetPriceFrom(string $price_from_key, array $params): float
    {
        if (!is_numeric($params[$price_from_key])) {
            throw new RequestFieldTypeException("Нижняя граница стоимости товара имеет нечисловой тип");
        }
        return (float)$params[$price_from_key];
    }

    protected function prepareAndGetPriceTo(string $price_to_key, array $params): float
    {
        if (!is_numeric($params[$price_to_key])) {
            throw new RequestFieldTypeException("Верхняя граница стоимости товара имеет нечисловой тип");
        }
        return (float)$params[$price_to_key];
    }

    protected function prepareAndGetInStock(string $in_stock_key, array $params): bool
    {
        $available_in_stock_params =
        [   
            'yes' => true,
            'no' => false,
            'y' => true,
            'n' => false,
        ];

        if (!isset($available_in_stock_params[$params[$in_stock_key]])) {
            throw new RequestFieldTypeException("Параметр \"в наличии\" имеет некорректное значение");
        }
        return $available_in_stock_params[$params[$in_stock_key]];

    }    



}