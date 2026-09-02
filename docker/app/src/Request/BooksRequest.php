<?php
declare(strict_types=1);
namespace App\Request;

class BooksRequest
{
    private ?string $book_name;

    private ?string $category;

    private ?float $book_price_from;

    private ?float $book_price_to;

    private ?bool $in_stock;

    private ?int $from;

    private ?int $size;

    public static function make(array $params): BooksRequest
    {
        return new BooksRequest($params);
    }

    public function __construct(array $params)
    {

        $this->book_name = $params['book_name'] ?? null;
        $this->category = $params['category'] ?? null;
        $this->book_price_from = $params['book_price_from'] ?? null;
        $this->book_price_to = $params['book_price_to'] ?? null;
        $this->in_stock = $params['in_stock'] ?? null;
        $this->from = $params['from'] ?? 100;
        $this->size = $params['size'] ?? 0;
    }

    public function getBookName(): ?string
    {
        return $this->book_name;
    }

    public function setBookName(string $book_name): static
    {
        $this->book_name = $book_name;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getBookPriceFrom(): ?float
    {
        return $this->book_price_from;
    }

    public function setBookPriceFrom(float $book_price_from): static
    {
        $this->book_price_from = $book_price_from;
        return $this;
    }

    public function getBookPriceTo(): ?float
    {
        return $this->book_price_to;
    }

    public function setBookPriceTo(float $book_price_to): static
    {
        $this->book_price_to = $book_price_to;
        return $this;
    }

    public function isInStock(): ?bool
    {
        return $this->in_stock;
    }

    public function setInStock(bool $in_stock): static
    {
        $this->in_stock = $in_stock;
        return $this;
    }

    public function getFrom(): ?int
    {
        return $this->from;
    }

    public function setFrom(int $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;
        return $this;
    }

}