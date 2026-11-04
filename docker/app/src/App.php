<?php
declare(strict_types=1);
namespace App;

use App\Builder\Http\ResponseBuilder;
use App\Client\ElasticClient;
use App\Client\MongoClient;
use App\Client\RedisClient;
use App\Constants\ClientConstantList;
use App\Controller\EventController;
use App\Exception\BadDataExceptionInterface;
use App\Exception\Http\BadRequestHttpException;
use App\Exception\Http\MethodNotAllowedHttpException;
use App\Exception\Http\NotFoundHttpException;
use App\Exception\ConflictExceptionInterface;
use App\Exception\Http\ConflictHttpException;
use App\Exception\Http\HttpException;
use App\Exception\NotFoundExceptionInterface;
use App\Http\Header;
use App\Http\Response;
use App\Model\Client\ElasticClientValue;
use App\Model\Client\MongoClientValue;
use App\Model\Client\RedisClientValue;
use App\Repository\ElasticEventRepository;
use App\Repository\EventRepositoryInterface;
use App\Repository\MongoEventRepository;
use App\Repository\RedisRepository;
use DI\Container;
use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;



use function FastRoute\simpleDispatcher;

class App
{
    public function run(array $params)
    {   
        $responseBuilder = ResponseBuilder::makeInstance();

        try {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->useAutowiring(true);

            $this->setClientMode($containerBuilder, $params['client_mode'], $params['client']);        

            $container = $containerBuilder->build();
            $dispatcher = simpleDispatcher(function(RouteCollector $r) {
                $r->addRoute('GET', '/api/v1/event/{event_uid:\d+}', ['controller' => EventController::class, 'action' => 'getEvent',]);
                $r->addRoute('POST', '/api/v1/event', ['controller' => EventController::class, 'action' => 'createEvent']);
                $r->addRoute('PATCH', '/api/v1/event/{event_uid:\d+}', ['controller' => EventController::class, 'action' => 'changeEvent']);
                $r->addRoute('DELETE', '/api/v1/event/{event_uid:\d+}', ['controller' => EventController::class, 'action' => 'removeEvent']);
                $r->addRoute('POST', '/api/v1/event/get', ['controller' => EventController::class, 'action' => 'getEventsByConditionList']);
                $r->addRoute('DELETE', '/api/v1/event', ['controller' => EventController::class, 'action' => 'clearEvents']);    
            });
            $action_list =
            [
                'getEvent' => function(EventController $eventController, array $args = []): Response {
                    $event_uid = $args['event_uid'];
                    return $eventController->getEvent($event_uid);
                },
                'createEvent' => function(EventController $eventController, array $args = []): Response {
                    $request_data = $this->prepareData($this->getRequestData());
                    return $eventController->createEvent($request_data);
                },
                'changeEvent' => function(EventController $eventController, array $args = []): Response {
                    $request_data = $this->prepareData($this->getRequestData());
                    $event_uid = $args['event_uid'];
                    return $eventController->changeEvent($event_uid, $request_data);
                },
                'removeEvent' => function(EventController $eventController, array $args = []): Response {
                    $event_uid = $args['event_uid'];
                    return $eventController->removeEvent($event_uid);
                },
                'getEventsByConditionList' => function(EventController $eventController, array $args = []): Response {
                    $request_data = $this->prepareData($this->getRequestData());
                    return $eventController->getEventsByConditionList($request_data);
                },
                'clearEvents' => function(EventController $eventController, array $args = []): Response {
                    return $eventController->clearEvents();
                },
            ];

            $http_method = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];

            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);

            $route_info = $dispatcher->dispatch($http_method, $uri);

            switch($route_info[0]) {
                case Dispatcher::NOT_FOUND:
                    throw new NotFoundHttpException("Путь $uri не найден");                    
                break;

                case Dispatcher::METHOD_NOT_ALLOWED:
                    $allowed_methods_str = implode(", ", $route_info[1]);
                    throw new MethodNotAllowedHttpException("Данный путь поддерживает только следующие методы: $allowed_methods_str");
                break;

                case Dispatcher::FOUND:
                    $controller_params = $route_info[1];
                    $args = $route_info[2];

                    $controller_class = $controller_params['controller'];
                    $action = $controller_params['action'];

                    $controller = $container->get($controller_class);
                    
                    /** @var Response $response */
                    $response = $action_list[$action]($controller, $args);

                    $response->pushHeader(Header::makeInstance('Content-Type', 'application/json'));
                    $response->send();
                    
                break;
            }



        }
        catch(\Exception $e) {

            $exception_interface_list = 
            [
                NotFoundExceptionInterface::class => NotFoundHttpException::class,
                BadDataExceptionInterface::class => BadRequestHttpException::class,
                ConflictExceptionInterface::class => ConflictHttpException::class,
            ];

            $current_exception_interface_list = class_implements($e);
            
            $targetException = $e;

            foreach ($exception_interface_list as $interface_name => $exception_class) {
                if (isset($current_exception_interface_list[$interface_name])) {
                    $targetException = new $exception_class($e->getMessage());
                    break;
                }
            }
            
            if ($targetException instanceof HttpException) {
                $responseBuilder->setStatusCode($targetException->getStatusCode());
            }
            else {
                $responseBuilder->setStatusCode(500);
            }
            
            $responseBuilder->addHeaders(['Content-Type' => 'application/json']);
            $responseBuilder->asJson(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            $response = $responseBuilder->getResponse();

            $response->send();

        }


    }

    protected function setClientMode(ContainerBuilder $containerBuilder, string $client_mode, array $client_params)
    {   
        if ($client_mode === ClientConstantList::REDIS_MODE) {
            $containerBuilder->addDefinitions(
                [
                    EventRepositoryInterface::class => \DI\autowire(RedisRepository::class),
                    RedisClient::class => function() use ($client_params) {
                        $redisClientValue = RedisClientValue::makeInstanceFromArray($client_params);
                        return RedisClient::makeInstance($redisClientValue);
                    },
                ]);
            
        }

        if ($client_mode === ClientConstantList::ELASTIC_MODE) {
            $containerBuilder->addDefinitions(
                [
                    EventRepositoryInterface::class => \DI\autowire(ElasticEventRepository::class),
                    ElasticClient::class => function() use ($client_params) {
                        $elasticClientValue = ElasticClientValue::makeInstanceFromArray($client_params);
                        return ElasticClient::makeInstance($elasticClientValue);
                    }
                ]
            );
        }

        if ($client_mode === ClientConstantList::MONGO_MODE) {
            $containerBuilder->addDefinitions(
                [
                    EventRepositoryInterface::class => \DI\autowire(MongoEventRepository::class),
                    MongoClient::class => function() use ($client_params) {
                        $mongoClientValue = MongoClientValue::makeInstanceFromArray($client_params);
                        return MongoClient::makeInstance($mongoClientValue);
                    }
                ]
            );
        }
    }
    

    protected function prepareData(array $data): array
    {
        $prepared_data = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $prepared_data[$key] = $this->prepareData($value);
            }
            else if (is_string($value)) {
                $prepared_data[$key] = trim(htmlspecialchars($value));
            }
            else {
                $prepared_data[$key] = $value;
            }
        }

        return $prepared_data;
    }

    protected function getRequestData(): array
    {
        $request_data = file_get_contents('php://input');
        return json_decode($request_data, true);
    }

}