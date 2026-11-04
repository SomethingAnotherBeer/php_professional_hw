<?php
declare(strict_types=1);
namespace App\DataMapper;

use App\Interface\IdentityItemInterface;

class Product implements IdentityItemInterface
{

    private int $product_id;

    private int $category_id;

    private string $product_name;

    private float $product_price;

    private bool $in_stock;

    private bool $is_deleted = false;


    public function __construct(
        int $product_id,
        int $category_id,
        string $product_name,
        float $product_price,
        bool $in_stock
    )
    {
        $this->product_id = $product_id;
        $this->category_id = $category_id;
        $this->product_name = $product_name;
        $this->product_price = $product_price;
        $this->in_stock = $in_stock;
    }


    public static function makeInstance(
    int $product_id,
    int $category_id,
    string $product_name,
    float $product_price,
    bool $in_stock
    ): Product
    {
        return new Product($product_id, $category_id, $product_name, $product_price, $in_stock);
    }




    public function getId(): int|string
    {
        return $this->product_id;
    }


    public function getProductId(): int
    {
        return $this->product_id;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getProductName(): string
    {
        return $this->product_name;
    }

    public function setProductName(string $product_name): static
    {
        $this->product_name = $product_name;

        return $this;
    }

    public function getProductPrice(): float
    {
        return $this->product_price;
    }

    public function setProductPrice(float $product_price): static
    {
        $this->product_price = $product_price;

        return $this;
    }

    public function isInStock(): bool
    {
        return $this->in_stock;
    }

    public function setInStock(bool $in_stock): static
    {
        $this->in_stock = $in_stock;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

    public function setDeleted(): void
    {
        $this->is_deleted = true;
    }
    

    public function replace(Product $product): void
    {
        $this->product_id = $product->getId();
        $this->category_id = $product->getCategoryId();
        $this->product_name = $product->getProductName();
        $this->product_price = $product->getProductPrice();
        $this->in_stock = $product->isInStock();
    }


    public function __toString(): string
    {
        $product_params = 
        [
            'product_id' => $this->product_id,
            'category_id' => $this->category_id,
            'product_name' => $this->product_name,
            'product_price' => $this->product_price,
            'in_stock' => (true === $this->in_stock) ? "true" : "false",
        ];

        $string_product_params = [];
        foreach ($product_params as $key => $value) {
            $string_product_params[] = "{$key}: {$value}";
        }

        return implode("\n", $string_product_params);
    }


}