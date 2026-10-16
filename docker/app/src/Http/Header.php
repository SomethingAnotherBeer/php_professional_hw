<?php
declare(strict_types=1);
namespace App\Http;

class Header
{
    private string $header_key;
    private string $header_value;

    public static function makeInstance(string $header_key, string $header_value): Header
    {
        return new Header($header_key, $header_value);
    }

    public function __construct(string $header_key, string $header_value)
    {
        $this->header_key = $header_key;
        $this->header_value = $header_value;
    }

    public function getHeaderKey(): string
    {
        return $this->header_key;
    }

    public function getHeaderValue(): string
    {
        return $this->header_value;
    }

    public function asString(): string
    {
        return "{$this->header_key}:{$this->header_value}";
    }

}