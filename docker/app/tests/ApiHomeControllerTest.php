<?php
declare(strict_types=1);
namespace Test;

use GuzzleHttp\Client;
use Override;
use PHPUnit\Framework\TestCase;

class ApiHomeControllerTest extends TestCase
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
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            'string' => 'some string',
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);
       
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Строка корректна", $data['response']);
    }

    public function testVerifyStringStringIsNotFound(): void
    {
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);
        
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Строка не найдена в теле запроса", $data['error']);
    }

    public function testVerifyStringStringIsEmpty(): void
    {
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            'string' => '',    
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Строка пустая", $data['error']);
    }

    public function testVerifyStringBracketsIsIncorrectCaseOne(): void
    {
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            'string' => '(()',    
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено закрывающей скобки для открывающей на позиции 2", $data['error']);

    }

    public function testVerifyStringBracketsIsIncorrectCaseTwo(): void
    {
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            'string' => '(()))',    
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено открывающей скобки для закрывающей на позиции 5", $data['error']);

    }

    public function testVerifyStringBracketsIsIncorrectCaseThree(): void
    {
        $uri = "/api";
        $headers =
        [
            'Content-Type' => 'application/json',
        ];
        $request_body = 
        [
            'string' => ')(',    
        ];

        $response = $this->httpClient->request('POST', $uri, ['json' => $request_body, 'headers' => $headers]);
        $data = json_decode($response->getBody()->getContents(), true);
        
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Не найдено закрывающей скобки для открывающей на позиции 2\nНе найдено открывающей скобки для закрывающей на позиции 1", $data['error']);
    }

}