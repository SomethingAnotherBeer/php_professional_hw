<?php
declare(strict_types=1);
namespace App\DataMapper;

use App\Interface\IdentityItemInterface;

class Category implements IdentityItemInterface
{    
    private int $category_id;

    private string $category_name;

    /** @var Product[] $product_list */
    private array $product_list;

    private bool $is_deleted = false;

    public function __construct(int $category_id, string $category_name, array $product_list = [])
    {

        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->product_list = $product_list;

    }

    public static function makeInstance(int $category_id, string $category_name, array $product_list = []): Category
    {
        return new Category($category_id, $category_name, $product_list);
    }


    public function getId(): int|string
    {
        return $this->category_id;
    }


    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    public function setCategoryName(string $category_name): static
    {
        $this->category_name = $category_name;

        return $this;
    }

    /**
     * @return Product[]
     */
    public function getProductList(): array
    {
        return $this->product_list;
    }

    /**
     * @param Product[] $product_list
     * @return static
     */
    public function setProductList(array $product_list): static
    {
        $this->product_list = $product_list;

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

    public function replace(Category $category): void
    {
        $this->category_id = $category->getCategoryid();
        $this->category_name = $category->getCategoryName();
        $this->product_list = $category->getProductList();
    }

}