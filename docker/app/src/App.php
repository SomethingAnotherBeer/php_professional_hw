<?php
declare(strict_types=1);
namespace App;

use App\Controller\ApiHomeController;
use App\Controller\ApiLoginController;
use App\Controller\Controller;
use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\StringController;
use App\Exception\Http\MethodNotAllowedException;
use App\Exception\Http\RouteNotFoundException;
use App\Exception\Service\MethodNotFoundException;
use App\Infrastructure\Connection;
use App\Interface\Exception\BadDataExceptionInterface;
use App\Interface\Exception\NotAllowedExceptionInterface;
use App\Interface\Exception\NotFoundExceptionInterface;
use App\Interface\Exception\ServerExceptionInterface;
use App\Interface\Exception\UnauthorizedExceptionInterface;
use App\Interface\Service\IsFactoryInterface;
use App\Service\LoginService;
use App\Service\StringService;
use FastRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

use function FastRoute\simpleDispatcher;

class App
{
    public static function makeInstance(): App
    {
        return new App();
    }

    public function process()
    {
        $request = Request::createFromGlobals();
        $storage = new NativeSessionStorage();
        $session = new Session($storage);
        $request->setSession($session);

        $connection_params =
        [
            'db_host' => $_ENV['DB_HOST'],
            'db_name' => $_ENV['DB_NAME'],
            'db_user' => $_ENV['DB_USER'],
            'db_password' => $_ENV['DB_PASSWORD'],

        ];
        $connection = Connection::makeInstance()->initializedConnection($connection_params);

        try {

            $dispatcher = simpleDispatcher(function(FastRoute\RouteCollector $r) use ($connection) {
                    $r->addRoute('POST', '/', ['class' => HomeController::class, 'method' => 'execute', 'args' => [StringService::class]]);
                    $r->addRoute('GET', '/', ['class' => HomeController::class, 'method' => 'index', 'args' => [StringService::class]]);
                    $r->addRoute('POST', '/api', ['class' => ApiHomeController::class, 'method' => 'execute', 'args' => [StringService::class]]);
                    $r->addRoute('GET', '/api', ['class' => ApiHomeController::class, 'method' => 'index', 'args' => [StringService::class]]);
                    $r->addRoute('POST', '/login', ['class' => LoginController::class, 'method' => 'login', 'instancedArgs' => [new LoginService($connection)]]);
                    $r->addRoute('POST', '/logout', ['class' => LoginController::class, 'method' => 'logout']);
                    $r->addRoute('POST', '/api/login', ['class' => ApiLoginController::class, 'method' => 'login', 'instancedArgs' => [new LoginService($connection)]]);
                    $r->addRoute('POST', '/api/logout', ['class' => ApiLoginController::class, 'method' => 'logout']);
            });

            $http_method = $request->getMethod();
            $uri = $request->getRequestUri();

            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }

            $routeInfo = $dispatcher->dispatch($http_method, $uri);

            switch ($routeInfo[0]) {
                case FastRoute\Dispatcher::NOT_FOUND:
                    throw new RouteNotFoundException("Ресурс $uri не найден");
                    
                    break;
                case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    throw new MethodNotAllowedException("Метод $http_method не является приемлемым для данного ресурса");
                    break;
                case FastRoute\Dispatcher::FOUND:
                    $handler_params = $routeInfo[1];
                    $vars = $routeInfo[2];

                    if (is_subclass_of($handler_params['class'], Controller::class)) {
                        $instance = new $handler_params['class']($request);
                        if (!method_exists($instance, $handler_params['method'])) {
                            throw new MethodNotFoundException("Метод {$handler_params['method']} не найден в классе {$handler_params['class']}");
                        }
                        $method = $handler_params['method'];

                        $all_args = [];
                        if (array_key_exists('args', $handler_params) && count($handler_params['args']) > 0) {
                            $handler_args = [];
                            foreach ($handler_params['args'] as $arg) {
                                $handler_args[] = (is_subclass_of($arg, IsFactoryInterface::class)) ? $arg::makeInstance() : new $arg();
                            }
                            $all_args = array_merge($all_args, $handler_args);
                        }

                        if (array_key_exists('instancedArgs', $handler_params) && count($handler_params['instancedArgs']) > 0) {
                            $all_args = array_merge($all_args, $handler_params['instancedArgs']);
                        }

                        if (count($vars) > 0) {
                            $all_args = array_merge($all_args, $vars);
                        }

                        $response = $instance->$method(...$all_args);

                        if ($response instanceof Response) {
                            $response->prepare($request);
                            $response->send();
                        }
                        
                    }
            break;
        }

        }

    catch(\Exception $e) {

        $response = null;
        $content = null;
        $content_type = $request->getContentTypeFormat();

        if ("json" === $content_type) {
            $response = new JsonResponse();
            $content = json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            
        }
        else {
            $response = new Response();
            $content = $e->getMessage();
        }
        $response->setContent($content);
    
        $exception_code_list =
        [
            BadDataExceptionInterface::class => Response::HTTP_BAD_REQUEST,
            NotFoundExceptionInterface::class => Response::HTTP_NOT_FOUND,
            ServerExceptionInterface::class => Response::HTTP_INTERNAL_SERVER_ERROR,
            NotAllowedExceptionInterface::class => Response::HTTP_METHOD_NOT_ALLOWED,
            UnauthorizedExceptionInterface::class => Response::HTTP_UNAUTHORIZED,
        ];

        $current_code = 500;

        foreach ($exception_code_list as $exception_interface => $code) {
            if (is_subclass_of($e::class, $exception_interface)) {
                $current_code = $code;
                break;
            }
        }

        $response->setStatusCode($current_code);
        $response->prepare($request);
        $response->send();
        
    }
    

    }


}