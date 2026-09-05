<?php
declare(strict_types=1);
namespace App\View;

use App\ValueObject\BookList;
use App\ValueObject\Book;
use App\ValueObject\BookStockList;

class BooksCliView
{
    public function render(BookList $bookList)
    {
        
        $len_options = $this->getFieldsLen($bookList);

        foreach ($bookList as $book) {
            $output_params =
            [
                'output_sku' => $this->getFormattedSku($book, $len_options['sku']),
                'output_title' => $this->getFormattedTitle($book, $len_options['title']),
                'output_category' => $this->getFormattedCategory($book, $len_options['category']),
                'output_price' => $this->getFormattedPrice($book, $len_options['price']),
                'output_stock' => $this->getFormattedStock($book, $len_options['stock']),
            ];

            $output_params_str = implode(' | ', $output_params);
            $output_params_str_len = mb_strlen($output_params_str);

            echo $output_params_str, "\n", sprintf("%'_{$output_params_str_len}s", ''), "\n";
        
        }
    }


    public function getFieldsLen(BookList $bookList): array
    {
        $len_params = 
        [
            'sku' => 0,
            'title' => 0,
            'category' => 0,
            'price' => 0,
            'stock' => 0
        ];

        $min_len = 10;
        foreach ($bookList as $book) {
            $current_sku_len = null !== $book->sku ? $min_len + mb_strlen($book->sku, 'UTF-8') : $min_len;
            $current_title_len = null !== $book->title ? $min_len + mb_strlen($book->title, 'UTF-8') : $min_len;
            $current_category_len = null !== $book->category ? $min_len + mb_strlen($book->category, 'UTF-8') : $min_len;
            $current_price_len = null !== $book->price ? $min_len + mb_strlen((string)$book->price) : $min_len;
            $current_stock_len = null !== $book->bookStockList ? $min_len + $this->getStockLen($book->bookStockList) : $min_len;

            if ($current_sku_len > $len_params['sku']) {
                $len_params['sku'] = $current_sku_len;
            }
            if ($current_title_len > $len_params['title']) {
                $len_params['title'] = $current_title_len;
            }
            if ($current_category_len > $len_params['category']) {
                $len_params['category'] = $current_category_len;
            }
            if ($current_price_len > $len_params['price']) {
                $len_params['price'] = $current_price_len;
            }
            if ($current_stock_len > $len_params['stock']) {
                $len_params['stock'] = $current_stock_len;
            }

            
        }
        
        return $len_params;

    }



    protected function getStockLen(BookStockList $bookStockList): int
    {
        $max_len = 0;

        foreach ($bookStockList as $bookStock) {
            $max_len+= mb_strlen($bookStock->asString());
        }


        return $max_len;
    }



    private function getFormattedSku(Book $book, int $format_len): string
    {
        return $this->getFormatted($book->sku ?? '', $format_len);
    }

    private function getFormattedTitle(Book $book, int $format_len): string
    {
        return $this->getFormatted($book->title ?? '', $format_len);
    }

    private function getFormattedCategory(Book $book, int $format_len)
    {
        return $this->getFormatted($book->category, $format_len);
    }

    private function getFormattedPrice(Book $book, int $format_len): string
    {
        return (null !== $book->price) ? $this->getFormatted((string)round($book->price, 2), $format_len)
            : $this->getFormatted('', $format_len);
    }

    private function getFormattedStock(Book $book, int $format_len): string
    {
        $book_stock_list = [];
        foreach ($book->bookStockList as $bookStock) {
            $book_stock_list[] = $bookStock->asString();
        }
        $book_stock_list_str = implode(', ', $book_stock_list);
        return $this->getFormatted($book_stock_list_str, $format_len);
    }

    private function getFormatted(string $value, int $len): string
    {
        $current_len = mb_strlen($value);
        return $value . str_repeat(' ', $len - $current_len);
    }

}