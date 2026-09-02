<?php
declare(strict_types=1);
namespace App;

use App\CliController\BooksCliController;
use App\CliController\CliController;
use App\Exception\CliController\PathNotFoundException;
use App\Exception\Controller\ControllerMethodNotFoundException;
use App\Exception\Controller\ControllerMethodNotSpecifiedException;
use App\Exception\Controller\ControllerNotFoundException;
use App\Exception\Controller\ControllerNotSpecifiedException;

class App
{
    public function execute(array $params)
    {
        try {
            

            $controllers =
            [
                'books' => ['make_controller' => fn(array $params): CliController => BooksCliController::makeInstance($params), 'method' => 'books'],
            ];

            $controller_path = $params['path'] ?? null;
            $controller_method = $params['action'] ?? null;
            
            $controller_path = isset($params['path']) ? trim($params['path']) : null;
            $controller_method = isset($params['action']) ? trim($params['action']) : null;

            if (null === $controller_path) {
                throw new ControllerNotSpecifiedException("Контроллер не указан");
            }

            if (null === $controller_method) {
                throw new ControllerMethodNotSpecifiedException("Не указан метод контроллера");
            }

            
            if (!isset($controllers[$controller_path])) {
                throw new ControllerNotFoundException("Контроллер не найден");
            }

            $controller_params = $controllers[$params['path']];
            $controller = $controller_params['make_controller']($params);

            if (!method_exists($controller, $controller_params['method'])) {
                throw new ControllerMethodNotFoundException("Метод не найден");
            }

            $method = $controller_params['method'];
            $controller->$method();

            exit(0);

        }

        catch(\Exception $e) {
            echo $e->getMessage() . "\n";
            exit(1);
        }
    }


}