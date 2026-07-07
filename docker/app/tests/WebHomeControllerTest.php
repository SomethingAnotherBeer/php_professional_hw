<?php
declare(strict_types=1);
namespace Test;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Override;
class WebHomeControllerTest extends TestCase
{
    protected Client $httpClient;

    #[Override]
    protected function setUp(): void
    {
        $this->httpClient = new Client(
            [
                'base_uri' => 'http://nginx_main',
                'http_errors' => false,
            ]);

        
    }


    public function testVerifyStringSuccessfully(): void
    {
        $uri = "/";
        $request_body =
        [
            'string' => 'some string',
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Строка корректна", $data);

    }

    public function testVerifyStringStringIsNotFound(): void
    {
        $uri = "/";
        $request_body =
        [
            
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Строка не найдена в теле запроса", $data);
    }

    public function testVerifyStringStringIsEmpty(): void
    {
        $uri = "/";
        $request_body =
        [
            'string' => '',
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Строка пустая", $data);
    }


    public function testVerifyStringBracketsIsIncorrectCaseOne(): void
    {
        $uri = "/";
        $request_body =
        [
            'string' => '(()',
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено закрывающей скобки для открывающей на позиции 2", $data);
    }

    public function testVerifyStringBracketsIsIncorrectCaseTwo(): void
    {
        $uri = "/";
        $request_body =
        [
            'string' => '(()))', 
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено открывающей скобки для закрывающей на позиции 5", $data);
        
    }

    public function testVerifyStringBracketsIsIncorrectCaseThree(): void
    {
        $uri = "/";
        $request_body =
        [
            'string' => ')(', 
        ];
        $headers = 
        [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $response = $this->httpClient->request('POST', $uri, ['form_params' => $request_body, 'headers' => $headers]);
        $data = $response->getBody()->getContents();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено закрывающей скобки для открывающей на позиции 2\nНе найдено открывающей скобки для закрывающей на позиции 1", $data);
    }


}