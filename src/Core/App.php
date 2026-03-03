<?php
namespace Core;

use Request\Request;

class App
{
    private array $routes = [];

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

        $route = $this->routes[$requestUri][$requestMethod];
        $className =  $route['class'];
        $methodName = $route['method'];
        $requestClass = $route['request'] ?? \Request\Request::class;

        $controller = new $className();
        $request = new $requestClass($requestMethod, $requestUri, $_POST);

        // Если POST — передаём данные
        if ($requestMethod === 'POST') {
            $controller->$methodName($request);
        } else {
            $controller->$methodName();
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
