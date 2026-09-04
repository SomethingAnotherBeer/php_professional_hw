<?php
declare(strict_types=1);
namespace App\Factory\ValueObject;

use App\ValueObject\Book;
use App\ValueObject\BookList;
use App\ValueObject\BookStock;
use App\ValueObject\BookStockList;

class BookValueObjectFactory
{
    public function makeBookList(array $params): BookList
    {   
        /** Book[] $book_list */
        $book_list = [];
        foreach ($params as $book_params) {
            $book_list[] = $this->makeBook($book_params);
        }

        return new BookList($book_list);
    }

    public function makeBook(array $params): Book
    {
        $target_params = [];

        if (isset($params['sku']) && is_string($params['sku'])) {
            $target_params['sku'] = $params['sku'];
        }
        if (isset($params['title']) && is_string($params['title'])) {
            $target_params['title'] = $params['title'];
        }
        if (isset($params['category']) && is_string($params['category'])) {
            $target_params['category'] = $params['category'];
        }
        if (isset($params['price']) && (is_float($params['price']) || is_int($params['price']))) {
            $target_params['price'] = (float)$params['price'];
        }
        if (isset($params['stock']) && is_array($params['stock'])) {
            $target_params['stock'] = $this->makeBookStockList($params['stock']);
        }
        
        return new Book($target_params);       

    }

    public function makeBookStockList(array $params): BookStockList
    {   
        /** BookStock[] $book_stock_list */
        $book_stock_list = [];
        foreach ($params as $stock_params) {
            $book_stock_list[] = $this->makeBookStock($stock_params);
        }
        return new BookStockList($book_stock_list);

    }

    public function makeBookStock(array $stock_params): BookStock
    {
        $target_params = [];
        if (isset($stock_params['shop']) && is_string($stock_params['shop'])) {
            $target_params['shop'] = $stock_params['shop'];
        }
        if (isset($stock_params['stock']) && is_int($stock_params['stock'])) {
            $target_params['stock'] = $stock_params['stock'];
        }

        return new BookStock($target_params);
    }

}