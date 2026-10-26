<?php
declare(strict_types=1);
namespace App\Test\Controller;

use App\Exception\Event\EventAlreadyExistsException;
use App\Exception\Event\EventConditionListNotSpecifiedException;
use App\Exception\Event\EventNotFoundException;
use App\Exception\Event\EventPriorityNotSpecifiedException;
use App\Exception\Event\EventUidNotSpecifiedException;
use App\Exception\Event\EventValueListNotSpecifiedException;
use App\Exception\EventDTO\IntegerEventConditionKeysDiscoveredException;
use App\Exception\EventDTO\IntegerEventValueKeysDiscoveredException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Override;
use PHPUnit\Framework\TestCase;

class EventControllerTest extends TestCase
{
    private Client $client;
    
    
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->client = new Client([
            'base_uri' => 'http://nginx:80/',
            'timeout' => 1.0,
            'http_errors' => false,
        ]);

    }


    public function testCreateEvents(): void
    {
        $event_params_list = 
        [
            [
                'event_uid' => '111',
                'event_priority' => 20,
                'event_condition_list' =>
                [
                    'first_condition' => true,
                    'second_condition' => "qws111",
                    "third_condition" => 222,
                ],
                'event_value_list' => 
                [
                    'first_value' => 111,
                    'second_value' => "some value",
                ],
            ],

             [
                'event_uid' => '112',
                'event_priority' => 25,
                'event_condition_list' =>
                [
                    'first_condition' => true,
                    'second_condition' => "qws111",
                    "third_condition" => 222,
                ],
                'event_value_list' => 
                [
                    'first_value' => 111,
                    'second_value' => "some value",
                ],
            ],

            [
                'event_uid' => '113',
                'event_priority' => 30,
                'event_condition_list' =>
                [
                    'first_condition' => false,
                    'second_condition_one' => "qws111",
                    "third_condition" => 222,
                ],
                'event_value_list' => 
                [
                    'first_value' => 111,
                    'second_value' => "some value",
                ],
            ],
        ];

        /** @var ResponseInterface[] $response_list */
        $response_list = [];

        /** @var string[] $prepared_response_list */
        $prepared_response_list = [];

        foreach ($event_params_list as $event_params) {
            $response = $this->client->post('api/v1/event', ['json' => $event_params]);

            $this->assertSame(201, $response->getStatusCode());
            $this->assertSame(json_encode($event_params, JSON_UNESCAPED_UNICODE), $response->getBody()->getContents());
        }

    }

    public function testGetEventsByConditionList(): void
    {

        $expected = 
        [
             [
                'event_uid' => '112',
                'event_priority' => 25,
                'event_condition_list' =>
                [
                    'first_condition' => true,
                    'second_condition' => "qws111",
                    "third_condition" => 222,
                ],
                'event_value_list' => 
                [
                    'first_value' => 111,
                    'second_value' => "some value",
                ],
            ],
        ];

        $event_condition_list = 
        [
            'first_condition' => true,
            'second_condition' => "qws111",
            "third_condition" => 222,
        ];

        $response = $this->client->post("api/v1/event/get", ['json' => $event_condition_list]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($expected, JSON_UNESCAPED_UNICODE), $response->getBody()->getContents());

    }

    public function testGetEvent(): void
    {
        $event_uid = 111;
        $expected = 
         [
            'event_uid' => '111',
            'event_priority' => 20,
            'event_condition_list' =>
            [
                'first_condition' => true,
                'second_condition' => "qws111",
                "third_condition" => 222,
            ],
            'event_value_list' => 
            [
                'first_value' => 111,
                'second_value' => "some value",
            ],
        ];

        $response = $this->client->get("api/v1/event/{$event_uid}");

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($expected, JSON_UNESCAPED_UNICODE), $response->getBody()->getContents());

    }

    public function testChangeEvent(): void
    {
        $event_uid = 113;
        $expected =
         [
            'event_uid' => '113',
            'event_priority' => 25,
            'event_condition_list' =>
            [
                'first_condition' => true,
                'second_condition_one' => "qws112",
                "third_condition" => 222,
            ],
            'event_value_list' => 
            [
                'first_value' => 111,
                'second_value' => "some value one",
            ],
        ];

        $changed_params =
        [
            'event_priority' => 25,
            'event_condition_list' =>
            [
                'first_condition' => true,
                'second_condition_one' => "qws112",
                "third_condition" => 222,
            ],
            'event_value_list' => 
            [
                'first_value' => 111,
                'second_value' => "some value one",
            ],
        ];


        $response = $this->client->patch("api/v1/event/{$event_uid}", ['json' => $changed_params]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($expected, JSON_UNESCAPED_UNICODE), $response->getBody()->getContents());

    }

    public function testRemoveEvent(): void
    {
        $event_uid = 113;
        $expected =
         [
            'event_uid' => '113',
            'event_priority' => 25,
            'event_condition_list' =>
            [
                'first_condition' => true,
                'second_condition_one' => "qws112",
                "third_condition" => 222,
            ],
            'event_value_list' => 
            [
                'first_value' => 111,
                'second_value' => "some value one",
            ],
        ];

        $response = $this->client->delete("api/v1/event/{$event_uid}");

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode($expected, JSON_UNESCAPED_UNICODE), $response->getBody()->getContents());

    }

    public function testGetEventEventNotFound(): void
    {

        $event_uid = 114;

        $response = $this->client->get("api/v1/event/{$event_uid}");
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame("Событие не найдено", $response_args['error']);
    }

    public function testChangeEventEventNotFound(): void
    {
        $event_uid = 114;

        $changed_params =
        [
            'event_condition_list' =>
            [
                'condition_one' => true,
            ],
        ];

        $response = $this->client->patch("api/v1/event/{$event_uid}", ['json' => $changed_params]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame("Событие не найдено", $response_args['error']);
    }

    public function testDeleteEventEventNotFound(): void
    {
        $event_uid = 114;

        $response =  $this->client->delete("api/v1/event/{$event_uid}");
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame("Событие не найдено", $response_args['error']);
    }

    public function testCreateEventAlreadyExists(): void
    {
        $request_body = 
        [
            'event_uid' => '111',
            'event_priority' => 20,
            'event_condition_list' => 
            [
                'first_condition' => true,
            ],
            'event_value_list' =>
            [
                'first_value' => "some value",
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame("Событие с таким идентификатором уже существует в системе", $response_args['error']);
        

    }

    public function testCreateEventEventUidNotSpecified(): void
    {
        $request_body =
        [
            'event_priority' => 20,
            'event_condition_list' => 
            [
                'first_condition' => true,
            ],
            'event_value_list' =>
            [
                'first_value' => "some value",
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Не указан идентификатор события", $response_args['error']);

    }

    public function testCreateEventEventPriorityNotSpecified(): void
    {
        $request_body =
        [   
            'event_uid' => '114',
            'event_condition_list' => 
            [
                'first_condition' => true,
            ],
            'event_value_list' =>
            [
                'first_value' => "some value",
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Не указан приоритет события", $response_args['error']);
    }

    public function testCreateEventEventConditionListNotSpecified(): void
    {
        $request_body =
        [   
            'event_uid' => '114',
            'event_priority' => 20,
    
            'event_value_list' =>
            [
                'first_value' => "some value",
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Не указан список условий возникновения события", $response_args['error']);
    }

    public function testCreateEventEventValueListNotSpecified(): void
    {
        $request_body =
        [   
            'event_uid' => '114',
            'event_priority' => 20,
            'event_condition_list' => 
            [
                'first_condition' => true,
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Не указан список значений события", $response_args['error']);
    }


    public function testCreateEventIntegerEventConditionKeysDiscovered(): void
    {
        $request_body =
        [
            'event_uid' => '114',
            'event_priority' => 25,
            'event_condition_list' =>
            [
                0 => true,
                1 => "some",
            ],
            'event_value_list' =>
            [
                'first_value' => 'some value'
            ],
        ];


        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Список условий события должен иметь только строковые ключи. Обнаружены следующие целочисленные ключи: 0, 1", $response_args['error']);
    }

    public function testCreateEventIntegerEventValueKeysDiscovered(): void
    {
        $request_body =
        [
            'event_uid' => '114',
            'event_priority' => 25,
            'event_condition_list' =>
            [
                'first_condition' => true,
            ],

            'event_value_list' =>
            [
                0 => 111,
                1 => true,
            ],
        ];

        $response = $this->client->post("api/v1/event", ['json' => $request_body]);
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame("Список значений события должен иметь только строковые ключи. Обнаружены следующие целочисленные ключи: 0, 1", $response_args['error']);
    }


    public function testClearEvents(): void
    {
        $expected_message = "Успешно удалено 2 событий";
        
        $response = $this->client->delete("api/v1/event");
        $response_args = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($expected_message, $response_args['message']);

        
    }



}