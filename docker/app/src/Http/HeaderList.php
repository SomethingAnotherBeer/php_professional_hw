<?php
declare(strict_types=1);
namespace App\Http;

use Iterator;

class HeaderList implements Iterator
{
    /** @var Header[] $header_list */
    private array $header_list;

    public static function makeInstance(): HeaderList
    {
        return new HeaderList();
    }

    public function __construct()
    {
        $this->header_list = [];
    }

    public function pushHeader(Header $header): static
    {
        $this->header_list[$header->getHeaderKey()] = $header;
        return $this;
    }

    public function all(): array
    {
        return $this->header_list;
    }

    public function current(): Header
    {
        return current($this->header_list);
    }

    public function key(): int
    {
        return key($this->header_list);
    }

    public function next(): void
    {
        next($this->header_list);
    }

    public function rewind(): void
    {
        reset($this->header_list);
    }

    public function valid(): bool
    {
        return null !== key($this->header_list);
    }

}