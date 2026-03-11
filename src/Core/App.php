<?php
namespace Core;

use Request\Request;
use Service\Logger\LoggerServiceInterface;

class App
{
    private LoggerServiceInterface $loggerService;
    private array $routes = [];

    public function __construct(LoggerServiceInterface $loggerService)
    {
        $this->loggerService = $loggerService;
    }

    public function run()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Проверка существования маршрута
        if (!isset($this->routes[$requestUri])) {
            http_response_code(404);
            require_once './../View/404.php';
            return;
        }

        // Проверка поддержки метода
        if (!isset($this->routes[$requestUri][$requestMethod])) {
            echo "$requestMethod не поддерживается адресом $requestUri";
            return;
        }

        try {
            $route = $this->routes[$requestUri][$requestMethod];
            $className = $route['class'];
            $methodName = $route['method'];
            $requestClass = $route['request'] ?? \Request\Request::class;

            $controller = new $className();
            $request = new $requestClass($requestMethod, $requestUri, $_POST);

            // Вызов метода контроллера
            if ($requestMethod === 'POST') {
                $controller->$methodName($request);
            } else {
                $controller->$methodName();
            }

        } catch (\Throwable $exception) {
            $this->loggerService->error($exception);
            http_response_code(500);
            require_once './../View/500.php';
        }
    }

    public function addRoute(string $requestUri, string $requestMethod,string $className,string $methodName, string $requestClass = Request :: class): void
    {
        if(!isset($this->routes[$requestUri][$requestMethod]))
        {
            $this->routes[$requestUri][$requestMethod]['class'] = $className;
            $this->routes[$requestUri][$requestMethod]['method'] = $methodName;
            $this->routes[$requestUri][$requestMethod]['request'] = $requestClass;
        } else {
            echo "$requestMethod уже зарегестрирован для $requestUri" . "<br>";
        }
    }
}
