<?php
declare(strict_types=1);
namespace App\ValueObject;

class Book
{
    public readonly ?string $sku;

    public readonly ?string $title;

    public readonly ?string $category;

    public readonly ?float $price;

    public readonly ?BookStockList $bookStockList;

    public function __construct(array $params)
    {
        $this->sku = $params['sku'] ?? null;
        $this->title = $params['title'] ?? null;
        $this->category = $params['category'] ?? null;
        $this->price = $params['price'] ?? null;
        $this->bookStockList = $params['stock'] ?? null;
    }

    public function asArray(): array
    {
        $params = 
        [
            'sku' => $this->sku,
            'title' => $this->title,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => null !== $this->bookStockList ? $this->bookStockList->asArray() : null,
        ];
        return array_filter($params, fn(mixed $param) => null !== $param);
    }


}