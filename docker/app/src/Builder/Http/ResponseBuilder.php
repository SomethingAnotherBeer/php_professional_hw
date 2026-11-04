<?php
declare(strict_types=1);
namespace App\Builder\Http;

use App\Http\Header;
use App\Http\Response;

class ResponseBuilder
{
    private ?Response $response = null;
   
    public static function makeInstance(): ResponseBuilder
    {
        return new ResponseBuilder();
    }

    public function asJson(array $params): static
    {
        $this->checkWithInit();    

        $response_data = json_encode($params, JSON_UNESCAPED_UNICODE);
        $this->response->setResponseBody($response_data);

        return $this;

    }

    public function setStatusCode(int $status_code): static
    {
        $this->checkWithInit();
        $this->response->setStatusCode($status_code);

        return $this;
    }

    public function addHeaders(array $headers): static
    {
        $this->checkWithInit();

        foreach ($headers as $header_key => $header_value) {
            $this->response->pushHeader(Header::makeInstance($header_key, $header_value));
        }
        return $this;   
    }

    public function getResponse(): Response
    {
        $this->checkWithInit();
        return $this->response;
    }

    protected function checkWithInit(): void
    {
        if (!$this->response) {
            $this->init();
        }
    }


    protected function init(): void
    {
        $this->response = Response::makeInstance();
    }


}