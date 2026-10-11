<?php
declare(strict_types=1);
namespace App;

use App\Constants\ClientConstantList;
use App\Controller\EventController;
use App\Repository\EventRepositoryInterface;
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
        try {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->useAutowiring(true);

            $this->setClientMode($containerBuilder, $params['client_mode']);        

            $container = $containerBuilder->build();
            $dispatcher = simpleDispatcher(function(RouteCollector $r) {
                $r->addRoute('GET', '/api/v1/event/{event_id:\d+}', ['controller' => EventController::class, 'action' => 'getEvent']);
                $r->addRoute('POST', '/api/v1/event', ['controller' => EventController::class, 'action' => 'createEvent']);
                $r->addRoute('PATCH', '/api/v1/event/{event_id:\d+}', ['controller' => EventController::class, 'action' => 'changeEvent']);
                $r->addRoute('DELETE', '/api/v1/event/{event_id:\d+}', ['controller' => EventController::class, 'action' => 'removeEvent']);
                $r->addRoute('GET', '/api/v1/event', ['controller' => EventController::class, 'action' => 'getEventsByConditionList']);
                $r->addRoute('DELETE', '/api/v1/event', ['controller' => EventController::class, 'action' => 'clearEvents']);    
            });

            $http_method = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];

            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);

            $route_info = $dispatcher->dispatch($http_method, $uri);

            print_r($route_info);

            switch($route_info[0]) {
                case Dispatcher::NOT_FOUND:
                
                break;

                case Dispatcher::METHOD_NOT_ALLOWED:

                break;

                case Dispatcher::FOUND:

                break;
            }



        }
        catch(\Exception $e) {

        }


    }



    protected function setClientMode(ContainerBuilder $containerBuilder, string $client_mode)
    {       
        if ($client_mode === ClientConstantList::REDIS_MODE) {
            $containerBuilder->addDefinitions([EventRepositoryInterface::class => RedisRepository::class]);
        }
    }

}