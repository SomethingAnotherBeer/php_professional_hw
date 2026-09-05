<?php
declare(strict_types=1);
namespace App\ValueObject;

use Iterator;
use Override;

class BookStockList implements Iterator
{   
    /** @var BookStock[] $book_stock_list */
    private array $book_stock_list = [];
    private int $position;

    public function __construct(array $book_stock_list)
    {
        $this->book_stock_list = $book_stock_list;
        $this->position = 0;
    }

    /**
     * @return BookStock[]
     */
    public function all(): array
    {
        return $this->book_stock_list;
    }

    public function asArray(): array
    {
        return array_map(fn(BookStock $bookStock) => $bookStock->asArray(), $this->book_stock_list);
    }


    public function rewind(): void
    {
        $this->position = 0;        
    }

    public function current(): BookStock
    {
        return $this->book_stock_list[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    
    public function valid(): bool
    {
        return isset($this->book_stock_list[$this->position]);    
    }

}