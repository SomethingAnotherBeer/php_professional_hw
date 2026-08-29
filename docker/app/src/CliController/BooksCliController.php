<?php
declare(strict_types=1);
namespace App\CliController;

use App\Exception\CliController\PathNotFoundException;
use App\Factory\Request\BooksCliRequestFactory;
use App\Service\BooksService;
use App\ValueObject\Input\InputValue;

class BooksCliController
{
    protected BooksService $booksService;
    protected BooksCliRequestFactory $booksCliRequestFactory;

    public function __construct(BooksService $booksService, BooksCliRequestFactory $booksCliRequestFactory)
    {
        $this->booksService = $booksService;
        $this->booksCliRequestFactory = $booksCliRequestFactory;
    }


    public function books()
    {
        $input_steps =
        [
            'book_name' => 'Введите наименование книги: ',
            'book_category' => 'Введите наименование категории: ',
            'book_price_from' => 'Введите цену от: ',
            'book_price_to' => 'Введите цену до: ',
            'in_stock' => 'В наличии (y/n)',
        ];

        $input_params = [];


        foreach ($input_steps as $input_step_key => $input_step_value) {
            $input_params[$input_step_key] = readline($input_step_value);
        }
        $booksRequest = $this->booksCliRequestFactory->makeBooksRequest($input_params);
        

    }
}