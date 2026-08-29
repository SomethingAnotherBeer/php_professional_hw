<?php
declare(strict_types=1);
namespace App\Request;

use App\Exception\Request\RequestFieldNotSpecifiedException;

class BooksRequest
{
    public readonly string $book_name;

    public readonly ?string $category;

    public readonly float $book_price_from;

    public readonly float $book_price_to;

    public readonly bool $in_stock;

    public static function make(array $params): BooksRequest
    {
        return new BooksRequest($params);
    }

    public function __construct(array $params)
    {

        $this->book_name = $params['book_name'];
        $this->category = $params['category'] ?? null;
        $this->book_price_from = $params['book_price_from'];
        $this->book_price_to = $params['book_price_to'];
        $this->in_stock = $params['in_stock'];
    }


}