<?php
declare(strict_types=1);
namespace App\Http;

class Response
{
    private string $response_body = '';
    private int $status_code = 200;
    private HeaderList $headerList;

    public static function makeInstance()
    {
        return new Response();
    }

    public function __construct()
    {
        $this->response_body =  '';
        $this->status_code = 200;
        $this->headerList = HeaderList::makeInstance();
    }

    public function getResponseBody(): string
    {
        return $this->response_body;
    }

    public function setResponseBody(string $response_body): static
    {
        $this->response_body = $response_body;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $status_code): static
    {
        $this->status_code = $status_code;
        return $this;
    }
    
    public function getHeaderList(): HeaderList
    {
        return $this->headerList;
    }

    public function pushHeader(Header $header): static
    {
        $this->headerList->pushHeader($header);
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status_code);
        foreach ($this->getHeaderList() as $header) {
            header($header->asString());
        }
        echo $this->response_body;
    }

}