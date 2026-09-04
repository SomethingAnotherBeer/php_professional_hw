<?php
declare(strict_types=1);
namespace App\Service;


use App\Client\ElasticClient;
use App\Factory\ValueObject\BookValueObjectFactory;
use App\Request\BooksRequest;
use App\ValueObject\BookList;
use App\ValueObject\Input\InputValue;

class BooksService
{
    protected ElasticClient $client;
    protected BookValueObjectFactory $bookValueObjectFactory;

    public function __construct(ElasticClient $client, BookValueObjectFactory $bookValueObjectFactory)
    {
        $this->client = $client;
        $this->bookValueObjectFactory = $bookValueObjectFactory;
    }

    public function searchBooks(BooksRequest $booksRequest): BookList
    {
        $book_name = $booksRequest->getBookName();
        $category = $booksRequest->getCategory();
        $book_price_from = $booksRequest->getBookPriceFrom();
        $book_price_to = $booksRequest->getBookPriceTo();
        $in_stock = $booksRequest->isInStock();
        $from = $booksRequest->getFrom() ?? 0;
        $size = $booksRequest->getSize() ?? 100;

        $must_args = [];
        $filter_args = [];

        if (null !== $book_name) {
            $must_args[] = ['match' => ['title' => ['query' => $book_name, 'fuzziness' => 'AUTO']]];
        }

        if (null !== $category) {
            $must_args[] = ['match' => ['category' => $category]];
        }

        if (null !== $book_price_from || null !== $book_price_to) {
            $price_range = [];
            if (null !== $book_price_from) {
                $price_range['gte'] = $book_price_from;
            }
            if (null !== $book_price_to) {
                $price_range['lte'] = $book_price_to;
            }
            $filter_args[] = ['range' => ['price' => $price_range]];
        }

        if (null !== $in_stock && true === $in_stock) {
            $filter_args[] =
            [
                'nested' => 
                [
                    'path' => 'stock',
                    'query' => [
                        'range' => 
                        [
                            'stock.stock' => ['gt' => 0],
                        ],
                    ],
                ]
            ];
        }

        $searchBooksQuery =
        [   'from' => $from,
            'size' => $size,
            'query' => [
                'bool' => [
                    'must' => $must_args,
                    'filter' => $filter_args
                ]
            ]
        ];


        $response = $this->client->query($searchBooksQuery);
        $response = $response->asArray();
        //print_r($response);
        $response = $response['hits']['hits'] ?? [];
        $source_response_params = [];

        foreach ($response as $item) {
            $source_response_params[] = $item['_source'];
        }

        

        return $this->bookValueObjectFactory->makeBookList($source_response_params);


    }


}