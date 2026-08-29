<?php
declare(strict_types=1);
namespace App\Service;

use App\Client\ElasticClient;
use App\Request\BooksRequest;
use App\ValueObject\Input\InputValue;

class BooksService
{
    protected ElasticClient $elasticClient;

    public function __construct(ElasticClient $elasticClient)
    {
        $this->elasticClient = $elasticClient;
    }

    public function searchBooks(BooksRequest $booksRequest)
    {
        $book_name = $booksRequest->book_name;
        $category = $booksRequest->category;
        $book_price_from = $booksRequest->book_price_from;
        $book_price_to = $booksRequest->book_price_to;
        $in_stock = $booksRequest->in_stock;

        $request = [];

        if (null !== $book_name) {
            
        }

    }


}