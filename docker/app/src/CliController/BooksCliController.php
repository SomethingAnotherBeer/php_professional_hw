<?php
declare(strict_types=1);
namespace App\CliController;

use App\Client\ElasticClient;

use App\Factory\Request\BooksCliRequestFactory;
use App\Factory\ValueObject\BookValueObjectFactory;
use App\Service\BooksService;
use App\View\BooksCliView;

class BooksCliController extends CliController
{
    protected BooksService $booksService;
    protected BooksCliRequestFactory $booksCliRequestFactory;
    protected BooksCliView $booksCliView;

    public static function makeInstance(array $params): BooksCliController
    {
        $elasticClient = ElasticClient::makeClient($params['client_options'] ?? []);
        $booksService = new BooksService($elasticClient, new BookValueObjectFactory());
        $booksCliRequestFactory = new BooksCliRequestFactory();
        $booksCliView = new BooksCliView();

        return new BooksCliController($booksService, $booksCliRequestFactory, $booksCliView);
    }


    public function __construct(BooksService $booksService, BooksCliRequestFactory $booksCliRequestFactory, BooksCliView $booksCliView)
    {
        $this->booksService = $booksService;
        $this->booksCliRequestFactory = $booksCliRequestFactory;
        $this->booksCliView = $booksCliView;
    }


    public function books()
    {
        $input_steps =
        [
            'book_name' => 'Введите наименование книги: ',
            'category' => 'Введите наименование категории: ',
            'book_price_from' => 'Введите цену от: ',
            'book_price_to' => 'Введите цену до: ',
            'in_stock' => 'В наличии (y/n)',
        ];

        $input_params = [];


        foreach ($input_steps as $input_step_key => $input_step_value) {
            $input_params[$input_step_key] = readline($input_step_value);
        }
        $booksRequest = $this->booksCliRequestFactory->makeBooksRequest($input_params);

        $response = $this->booksService->searchBooks($booksRequest);
        $this->booksCliView->render($response);
        

        /*$next = '';
        if ($response->count() > 0) {
            $next = readline('Введите r для получения следующих записей или q для выхода');
        }

        $offset_size = 100;

        while ($response->count() > 0 && 'q' === $next) {
            $current_from = $booksRequest->getFrom() + $offset_size;
            $booksRequest->setFrom($current_from);

            $response = $this->booksService->searchBooks($booksRequest);
            $this->booksCliView->render($response);
            $next = readline('Введите r для получения следующих записей или q для выхода');

        }*/

    }

    
}