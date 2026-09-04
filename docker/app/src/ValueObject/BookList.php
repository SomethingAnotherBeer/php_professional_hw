<?php
declare(strict_types=1);
namespace App\ValueObject;

use Iterator;
use Override;

class BookList implements Iterator
{   
    /** @var Book[] $book_list */
    private array $book_list = [];

    private int $count;

    private int $position;

    /** @param Book[] $book_list */
    public function __construct(array $book_list)
    {
        $this->book_list = $book_list;
        $this->count = count($book_list);
        $this->position = 0;
    }

    /** @return Book[] */
    public function all(): array
    {
        return $this->book_list;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function asArray(): array
    {
        return array_map(fn(Book $book) => $book->asArray(), $this->book_list);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): Book
    {
        return $this->book_list[$this->position];
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
        return isset($this->book_list[$this->position]);
    }

    

}