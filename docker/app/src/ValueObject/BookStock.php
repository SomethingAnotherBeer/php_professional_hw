<?php
declare(strict_types=1);
namespace App\ValueObject;

class BookStock
{
    public readonly ?string $shop;

    public readonly ?int $stock;

    public function __construct(array $params)
    {
        $this->shop = $params['shop'] ?? null;
        $this->stock = $params['stock'] ?? null;
    }

    public function asArray(): array
    {
        $params = 
        [
            'shop' => $this->shop,
            'stock' => $this->stock,
        ];
        return array_filter($params, fn(mixed $param) => null !== $param);
    }

    public function asString(): string
    {
        return (null !== $this->shop && null !== $this->stock) ? "{$this->shop}: $this->stock" : "";
    }


}